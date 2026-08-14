<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $roleFilter = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    public ?int $selectedUserId = null;

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

    public function updatedRoleFilter(): void
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
            'roleFilter',
            'statusFilter',
        ]);

        $this->resetPage();
    }

    public function openSuspendForm(int $userId): void
    {
        $targetUser = $this->manageableUser($userId);

        abort_unless(
            $targetUser->status === UserStatus::Active,
            422
        );

        $this->selectedUserId = $targetUser->id;
        $this->actionType = 'suspend';
        $this->reason = '';

        $this->resetValidation();
    }

    public function closeActionForm(): void
    {
        $this->reset([
            'selectedUserId',
            'actionType',
            'reason',
        ]);

        $this->resetValidation();
    }

    public function suspendUser(): void
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
            $this->selectedUserId !== null
                && $this->actionType === 'suspend',
            422
        );

        $this->transitionUser(
            $this->selectedUserId,
            UserStatus::Active,
            UserStatus::Suspended,
            $validated['reason']
        );

        $this->closeActionForm();

        session()->flash(
            'success',
            'User account suspended successfully.'
        );
    }

    public function reactivateUser(int $userId): void
    {
        $this->transitionUser(
            $userId,
            UserStatus::Suspended,
            UserStatus::Active,
            'User account reactivated by administrator.'
        );

        session()->flash(
            'success',
            'User account reactivated successfully.'
        );
    }

    private function transitionUser(
        int $userId,
        UserStatus $expectedStatus,
        UserStatus $newStatus,
        string $reason
    ): User {
        $admin = $this->adminUser();

        return DB::transaction(function () use (
            $userId,
            $expectedStatus,
            $newStatus,
            $reason,
            $admin
        ): User {
            $targetUser = User::query()
                ->lockForUpdate()
                ->findOrFail($userId);

            $this->ensureManageable($targetUser, $admin);

            if ($targetUser->status !== $expectedStatus) {
                throw ValidationException::withMessages([
                    'action' => 'The user status changed before this action was completed.',
                ]);
            }

            $oldStatus = $targetUser->status;

            $targetUser->forceFill([
                'status' => $newStatus,
            ])->save();

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => "user.{$newStatus->value}",
                'entity_type' => $targetUser->getMorphClass(),
                'entity_id' => $targetUser->id,
                'metadata' => [
                    'target_user_name' => $targetUser->name,
                    'target_user_email' => $targetUser->email,
                    'target_user_role' => $targetUser->role->value,
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'reason' => $reason,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $targetUser;
        });
    }

    private function manageableUser(int $userId): User
    {
        $admin = $this->adminUser();

        $targetUser = User::query()->findOrFail($userId);

        $this->ensureManageable($targetUser, $admin);

        return $targetUser;
    }

    private function ensureManageable(
        User $targetUser,
        User $admin
    ): void {
        abort_if($targetUser->id === $admin->id, 403);

        abort_if($targetUser->role === UserRole::Admin, 403);

        abort_unless(
            in_array(
                $targetUser->role,
                [
                    UserRole::Customer,
                    UserRole::Provider,
                ],
                true
            ),
            403
        );
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

        $validRoles = [
            UserRole::Customer->value,
            UserRole::Provider->value,
        ];

        $validStatuses = array_map(
            fn(UserStatus $status): string => $status->value,
            UserStatus::cases()
        );

        $search = trim($this->search);

        $baseQuery = User::query()->whereIn('role', $validRoles);

        $users = (clone $baseQuery)
            ->with([
                'business' => fn($query) => $query
                    ->select([
                        'id',
                        'owner_id',
                        'name',
                        'status',
                    ])
                    ->withCount('services'),
            ])
            ->withCount('bookings')
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
                                );
                        }
                    );
                }
            )
            ->when(
                in_array($this->roleFilter, $validRoles, true),
                fn(Builder $query) => $query->where(
                    'role',
                    $this->roleFilter
                )
            )
            ->when(
                in_array(
                    $this->statusFilter,
                    $validStatuses,
                    true
                ),
                fn(Builder $query) => $query->where(
                    'status',
                    $this->statusFilter
                )
            )
            ->latest()
            ->paginate(10);

        $metrics = [
            'total' => (clone $baseQuery)->count(),

            'customers' => (clone $baseQuery)
                ->where('role', UserRole::Customer->value)
                ->count(),

            'providers' => (clone $baseQuery)
                ->where('role', UserRole::Provider->value)
                ->count(),

            'active' => (clone $baseQuery)
                ->where('status', UserStatus::Active->value)
                ->count(),

            'suspended' => (clone $baseQuery)
                ->where('status', UserStatus::Suspended->value)
                ->count(),
        ];

        return view(
            'livewire.admin.user-manager',
            [
                'users' => $users,
                'metrics' => $metrics,
                'roleOptions' => [
                    UserRole::Customer,
                    UserRole::Provider,
                ],
                'statusOptions' => UserStatus::cases(),
            ]
        );
    }
}
