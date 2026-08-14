<?php

namespace App\Livewire\Admin;

use App\Enums\BookingStatus;
use App\Enums\BusinessStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function mount(): void
    {
        $this->adminUser();
    }

    private function adminUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User && $user->isAdmin(),
            403
        );

        return $user;
    }

    public function render(): View
    {
        $this->adminUser();

        $storedStatusCounts = Booking::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bookingStatusCounts = collect(BookingStatus::cases())
            ->mapWithKeys(
                fn (BookingStatus $status): array => [
                    $status->value => (int) (
                        $storedStatusCounts[$status->value] ?? 0
                    ),
                ]
            )
            ->all();

        $userMetrics = [
            'total' => User::query()->count(),

            'customers' => User::query()
                ->where('role', UserRole::Customer->value)
                ->count(),

            'providers' => User::query()
                ->where('role', UserRole::Provider->value)
                ->count(),

            'admins' => User::query()
                ->where('role', UserRole::Admin->value)
                ->count(),

            'active' => User::query()
                ->where('status', UserStatus::Active->value)
                ->count(),

            'suspended' => User::query()
                ->where('status', UserStatus::Suspended->value)
                ->count(),
        ];

        $businessMetrics = [
            'total' => Business::query()->count(),

            'pending' => Business::query()
                ->where('status', BusinessStatus::Pending->value)
                ->count(),

            'active' => Business::query()
                ->where('status', BusinessStatus::Active->value)
                ->count(),

            'suspended' => Business::query()
                ->where('status', BusinessStatus::Suspended->value)
                ->count(),
        ];

        $bookingMetrics = [
            'total' => Booking::query()->count(),

            'today' => Booking::query()
                ->whereHas(
                    'slot',
                    fn (Builder $query) => $query->whereDate(
                        'starts_at',
                        today()
                    )
                )
                ->count(),

            'upcoming_active' => Booking::query()
                ->whereIn(
                    'status',
                    BookingStatus::activeValues()
                )
                ->upcoming()
                ->count(),

            'completed_value' => (float) Booking::query()
                ->where(
                    'status',
                    BookingStatus::Completed->value
                )
                ->sum('price'),
        ];

        $recentBookings = Booking::query()
            ->with([
                'customer:id,name,email',
                'service:id,business_id,name',
                'service.business:id,name',
                'slot:id,starts_at,ends_at',
            ])
            ->latest()
            ->limit(8)
            ->get();

        $recentActivity = ActivityLog::query()
            ->with('user:id,name,email')
            ->latest()
            ->limit(10)
            ->get();

        return view(
            'livewire.admin.dashboard',
            [
                'userMetrics' => $userMetrics,
                'businessMetrics' => $businessMetrics,
                'bookingMetrics' => $bookingMetrics,
                'bookingStatusCounts' => $bookingStatusCounts,
                'recentBookings' => $recentBookings,
                'recentActivity' => $recentActivity,
            ]
        );
    }
}