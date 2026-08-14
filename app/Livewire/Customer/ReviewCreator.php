<?php

namespace App\Livewire\Customer;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReviewCreator extends Component
{
    use AuthorizesRequests;

    public Booking $booking;

    public int $rating = 0;

    public string $comment = '';

    public function mount(Booking $booking): void
    {
        $booking->load([
            'service.business',
            'slot',
            'review',
        ]);

        $this->authorize('review', $booking);

        $this->booking = $booking;
    }

    protected function rules(): array
    {
        return [
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function createReview()
    {
        $validated = $this->validate();
        $user = $this->authenticatedUser();

        DB::transaction(function () use (
            $validated,
            $user
        ): void {
            $booking = Booking::query()
                ->with([
                    'service.business',
                    'slot',
                    'review',
                ])
                ->where('customer_id', $user->id)
                ->lockForUpdate()
                ->findOrFail($this->booking->id);

            $this->authorize('review', $booking);

            $comment = trim($validated['comment'] ?? '');

            $review = Review::create([
                'booking_id' => $booking->id,
                'customer_id' => $user->id,
                'rating' => (int) $validated['rating'],
                'comment' => $comment !== ''
                    ? $comment
                    : null,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'review.created',
                'entity_type' => $review->getMorphClass(),
                'entity_id' => $review->id,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_reference' =>
                        $booking->booking_reference,
                    'service_id' => $booking->service_id,
                    'rating' => $review->rating,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()
            ->route('customer.bookings.index')
            ->with(
                'success',
                'Thank you! Your review was submitted successfully.'
            );
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): View
    {
        $this->booking->load([
            'service.business',
            'slot',
        ]);

        return view('livewire.customer.review-creator');
    }
}