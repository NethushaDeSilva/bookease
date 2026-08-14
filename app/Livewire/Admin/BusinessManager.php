<?php

namespace App\Livewire\Admin;

use App\Enums\BusinessStatus;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BusinessManager extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $selectedBusinessId = null;

    public string $actionType = '';

    public string $reason = '';

    public function mount(): void
    {
        $this->adminUser();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
        ]);

        $this->resetPage();
    }

    public function approveBusiness(int $businessId): void
    {
        $this->transitionBusiness(
            $businessId,
            [BusinessStatus::Pending],
            BusinessStatus::Active,
            'Business approved by administrator.'
        );

        session()->flash(
            'success',
            'Business approved successfully.'
        );
    }

    public function reactivateBusiness(int $businessId): void
    {
        $this->transitionBusiness(
            $businessId,
            [BusinessStatus::Suspended],
            BusinessStatus::Active,
            'Business reactivated by administrator.'
        );

        session()->flash(
            'success',
            'Business reactivated successfully.'
        );
    }

    public function openSuspendForm(int $businessId): void
    {
        $business = Business::query()->findOrFail($businessId);

        $this->authorize('update', $business);

        abort_unless(
            in_array(
                $business->status,
                [
                    BusinessStatus::Pending,
                    BusinessStatus::Active,
                ],
                true
            ),
            422
        );

        $this->selectedBusinessId = $business->id;
        $this->actionType = 'suspend';
        $this->reason = '';

        $this->resetValidation();
    }

    public function closeActionForm(): void
    {
        $this->reset([
            'selectedBusinessId',
            'actionType',
            'reason',
        ]);

        $this->resetValidation();
    }

    public function suspendBusiness(): void
    {
        $validated = $this->validate([
            'reason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ]);

        abort_unless(
            $this->selectedBusinessId !== null
            && $this->actionType === 'suspend',
            422
        );

        $this->transitionBusiness(
            $this->selectedBusinessId,
            [
                BusinessStatus::Pending,
                BusinessStatus::Active,
            ],
            BusinessStatus::Suspended,
            $validated['reason']
        );

        $this->closeActionForm();

        session()->flash(
            'success',
            'Business suspended successfully.'
        );
    }

    private function transitionBusiness(
        int $businessId,
        array $allowedStatuses,
        BusinessStatus $newStatus,
        string $reason
    ): Business {
        $admin = $this->adminUser();

        return DB::transaction(function () use (
            $businessId,
            $allowedStatuses,
            $newStatus,
            $reason,
            $admin
        ): Business {
            $business = Business::query()
                ->with('owner')
                ->lockForUpdate()
                ->findOrFail($businessId);

            $this->authorize('update', $business);

            if (
                ! in_array(
                    $business->status,
                    $allowedStatuses,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'action' => 'The business status changed before this action was completed.',
                ]);
            }

            $oldStatus = $business->status;

            $business->update([
                'status' => $newStatus,
            ]);

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => "business.{$newStatus->value}",
                'entity_type' => $business->getMorphClass(),
                'entity_id' => $business->id,
                'metadata' => [
                    'business_name' => $business->name,
                    'owner_id' => $business->owner_id,
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'reason' => $reason,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $business;
        });
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

        $validStatuses = array_map(
            fn (BusinessStatus $status): string => $status->value,
            BusinessStatus::cases()
        );

        $search = trim($this->search);

        $businesses = Business::query()
            ->with('owner:id,name,email,status')
            ->withCount([
                'services',
                'services as active_services_count' =>
                    fn (Builder $query) => $query->where(
                        'is_active',
                        true
                    ),
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
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
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'owner',
                                    function (Builder $query) use ($search): void {
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
                                            );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                in_array(
                    $this->statusFilter,
                    $validStatuses,
                    true
                ),
                fn (Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->orderByRaw(
                "CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'active' THEN 2
                    WHEN 'suspended' THEN 3
                    ELSE 4
                END"
            )
            ->latest()
            ->paginate(10);

        $statusCounts = [
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

        return view(
            'livewire.admin.business-manager',
            [
                'businesses' => $businesses,
                'statusOptions' => BusinessStatus::cases(),
                'statusCounts' => $statusCounts,
            ]
        );
    }
}