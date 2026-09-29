<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\CancelBookingRequest;

class BookingController extends Controller
{
    /**
     * Display the authenticated customer's bookings.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Booking::class);

        $user = $request->user();

        abort_unless(
            $user instanceof User && ($user->isCustomer() || $user->isProvider()),
            403,
            'Only customers and providers can access their personal appointments.'
        );

        $validated = $request->validate([
            'status' => [
                'nullable',
                'in:pending,confirmed,completed,cancelled,rejected',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);

        $bookings = Booking::query()
            ->forCustomer($user)
            ->with([
                'service:id,name,duration_minutes',
                'slot:id,service_id,starts_at,ends_at',
            ])
            ->when(
                isset($validated['status']),
                fn($query) => $query->where(
                    'status',
                    $validated['status']
                )
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return BookingResource::collection($bookings);
    }

    /**
     * Create a new customer booking.
     */
    public function store(
        StoreBookingRequest $request
    ): JsonResponse {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $validated = $request->validated();

        $booking = DB::transaction(function () use (
            $validated,
            $user
        ): Booking {
            $slot = AvailabilitySlot::query()
                ->with('service.business')
                ->lockForUpdate()
                ->findOrFail($validated['slot_id']);

            Gate::authorize('view', $slot->service);

            if (
                ! $slot->is_active
                || ! $slot->starts_at->isFuture()
            ) {
                throw ValidationException::withMessages([
                    'slot_id' =>
                    'This appointment is no longer available.',
                ]);
            }

            $activeBookingCount = $slot
                ->bookings()
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->count();

            if ($activeBookingCount >= $slot->capacity) {
                throw ValidationException::withMessages([
                    'slot_id' =>
                    'This appointment has become fully booked.',
                ]);
            }

            $duplicateBookingExists = Booking::query()
                ->where('customer_id', $user->id)
                ->where('slot_id', $slot->id)
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->exists();

            if ($duplicateBookingExists) {
                throw ValidationException::withMessages([
                    'slot_id' =>
                    'You already have an active booking for this appointment.',
                ]);
            }

            $notes = trim($validated['notes'] ?? '');

            $booking = Booking::create([
                'booking_reference' =>
                $this->createReference(),

                'customer_id' => $user->id,

                'service_id' => $slot->service_id,

                'slot_id' => $slot->id,

                'status' => BookingStatus::Pending,

                'price' => $slot->service->price,

                'notes' => $notes !== '' ? $notes : null,
            ]);

            $booking->statusHistories()->create([
                'changed_by' => $user->id,
                'old_status' => null,
                'new_status' => BookingStatus::Pending,
                'reason' => 'Booking created through the API.',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'booking.created',
                'entity_type' => $booking->getMorphClass(),
                'entity_id' => $booking->id,
                'metadata' => [
                    'booking_reference' =>
                    $booking->booking_reference,
                    'service_id' => $booking->service_id,
                    'slot_id' => $booking->slot_id,
                    'status' => $booking->status->value,
                    'price' => $booking->price,
                    'source' => 'api',
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $booking;
        });

        $booking->load([
            'service:id,name,duration_minutes',
            'slot:id,service_id,starts_at,ends_at',
        ]);

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Cancel a customer's booking.
     */
    public function cancel(
        CancelBookingRequest $request,
        Booking $booking
    ): JsonResponse {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $validated = $request->validated();

        $booking = DB::transaction(function () use (
            $booking,
            $user,
            $validated
        ): Booking {
            $lockedBooking = Booking::query()
                ->with([
                    'slot',
                    'service.business',
                ])
                ->where('customer_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($booking->id);

            Gate::authorize('cancel', $lockedBooking);

            $oldStatus = $lockedBooking->status;

            $lockedBooking->update([
                'status' => BookingStatus::Cancelled,

                'cancellation_reason' =>
                $validated['reason'],

                'cancelled_at' => now(),
            ]);

            $lockedBooking->statusHistories()->create([
                'changed_by' => $user->id,

                'old_status' => $oldStatus,

                'new_status' => BookingStatus::Cancelled,

                'reason' => $validated['reason'],
            ]);

            ActivityLog::create([
                'user_id' => $user->id,

                'action' => 'booking.cancelled',

                'entity_type' =>
                $lockedBooking->getMorphClass(),

                'entity_id' => $lockedBooking->id,

                'metadata' => [
                    'booking_reference' =>
                    $lockedBooking->booking_reference,

                    'old_status' => $oldStatus->value,

                    'new_status' =>
                    BookingStatus::Cancelled->value,

                    'reason' => $validated['reason'],

                    'source' => 'api',
                ],

                'ip_address' => request()->ip(),

                'user_agent' => request()->userAgent(),
            ]);

            return $lockedBooking;
        });

        $booking->load([
            'service:id,name,duration_minutes',
            'slot:id,service_id,starts_at,ends_at',
        ]);

        return (new BookingResource($booking))
            ->additional([
                'message' =>
                'The booking was cancelled successfully.',
            ])
            ->response();
    }

    /**
     * Display one booking belonging to the customer.
     */
    public function show(
        Request $request,
        Booking $booking
    ): BookingResource {
        Gate::authorize('view', $booking);

        $user = $request->user();

        abort_unless(
            $user instanceof User
                && ($user->isCustomer() || $user->isProvider())
                && $booking->customer_id === $user->id,
            403,
            'You can only access your own personal appointments here.'
        );

        $booking->load([
            'service:id,name,duration_minutes',
            'slot:id,service_id,starts_at,ends_at',
        ]);

        return new BookingResource($booking);
    }

    /**
     * Generate a unique booking reference.
     */
    private function createReference(): string
    {
        do {
            $reference = 'BE-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(Str::random(8));
        } while (
            Booking::query()
            ->where('booking_reference', $reference)
            ->exists()
        );

        return $reference;
    }
}
