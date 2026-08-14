<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use App\Enums\BookingStatus;
use App\Http\Requests\Api\Provider\UpdateBookingStatusRequest;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display bookings belonging to the provider's business.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        Gate::authorize('viewAny', Booking::class);

        $user = $request->user();

        abort_unless(
            $user instanceof User && $user->isProvider(),
            403,
            'Only service providers can access this endpoint.'
        );

        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                'in:pending,confirmed,completed,cancelled,rejected',
            ],

            'date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ]);

        $search = trim($validated['search'] ?? '');

        $perPage = (int) ($validated['per_page'] ?? 15);

        $bookings = Booking::query()
            ->whereHas(
                'service.business',
                fn(Builder $query) => $query->where(
                    'owner_id',
                    $user->id
                )
            )
            ->with([
                'customer:id,name,email',

                'service:id,business_id,name,duration_minutes',

                'service.business:id,name,owner_id,address',

                'slot:id,service_id,starts_at,ends_at',
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'booking_reference',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'customer',
                                    fn(Builder $query) =>
                                    $query
                                        ->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'email',
                                            'like',
                                            "%{$search}%"
                                        )
                                )
                                ->orWhereHas(
                                    'service',
                                    fn(Builder $query) =>
                                    $query->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                );
                        }
                    );
                }
            )
            ->when(
                isset($validated['status']),
                fn(Builder $query) => $query->where(
                    'status',
                    $validated['status']
                )
            )
            ->when(
                isset($validated['date']),
                fn(Builder $query) => $query->whereHas(
                    'slot',
                    fn(Builder $query) => $query->whereDate(
                        'starts_at',
                        $validated['date']
                    )
                )
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return BookingResource::collection($bookings);
    }

    /**
     * Update a provider-owned booking's status.
     */
    public function updateStatus(
        UpdateBookingStatusRequest $request,
        Booking $booking
    ): JsonResponse {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $validated = $request->validated();

        $newStatus = BookingStatus::from(
            $validated['status']
        );

        [$ability, $defaultReason] = match ($newStatus) {
            BookingStatus::Confirmed => [
                'confirm',
                'Booking confirmed by provider.',
            ],

            BookingStatus::Rejected => [
                'reject',
                'Booking rejected by provider.',
            ],

            BookingStatus::Completed => [
                'complete',
                'Service marked as completed by provider.',
            ],

            BookingStatus::Cancelled => [
                'cancel',
                'Booking cancelled by provider.',
            ],

            default => abort(422, 'Unsupported status transition.'),
        };

        $reason = trim($validated['reason'] ?? '');

        if ($reason === '') {
            $reason = $defaultReason;
        }

        $booking = DB::transaction(function () use (
            $booking,
            $user,
            $newStatus,
            $ability,
            $reason
        ): Booking {
            $lockedBooking = Booking::query()
                ->whereHas(
                    'service.business',
                    fn(Builder $query) => $query->where(
                        'owner_id',
                        $user->id
                    )
                )
                ->with([
                    'slot',
                    'service.business',
                ])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            Gate::authorize($ability, $lockedBooking);

            $oldStatus = $lockedBooking->status;

            $updateData = [
                'status' => $newStatus,
            ];

            if ($newStatus === BookingStatus::Cancelled) {
                $updateData['cancellation_reason'] = $reason;
                $updateData['cancelled_at'] = now();
            }

            $lockedBooking->update($updateData);

            $lockedBooking->statusHistories()->create([
                'changed_by' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,

                'action' =>
                "booking.{$newStatus->value}",

                'entity_type' =>
                $lockedBooking->getMorphClass(),

                'entity_id' => $lockedBooking->id,

                'metadata' => [
                    'booking_reference' =>
                    $lockedBooking->booking_reference,

                    'old_status' => $oldStatus->value,

                    'new_status' => $newStatus->value,

                    'reason' => $reason,

                    'source' => 'api',
                ],

                'ip_address' => request()->ip(),

                'user_agent' => request()->userAgent(),
            ]);

            return $lockedBooking;
        });

        $booking->load([
            'customer:id,name,email',

            'service:id,business_id,name,duration_minutes',

            'service.business:id,name,owner_id,address',

            'slot:id,service_id,starts_at,ends_at',
        ]);

        return (new BookingResource($booking))
            ->additional([
                'message' =>
                'The booking status was updated successfully.',
            ])
            ->response();
    }
}
