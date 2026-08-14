<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ActivityLogViewer extends Component
{
    use WithPagination;

    private const ACTION_CATEGORIES = [
        'booking',
        'business',
        'user',
        'review',
        'availability',
        'service',
    ];

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $categoryFilter = '';

    public string $dateFilter = '';

    public ?int $selectedLogId = null;

    public function mount(): void
    {
        $this->adminUser();
    }

    public function updatedSearch(): void
    {
        $this->selectedLogId = null;
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->selectedLogId = null;
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->validateOnly('dateFilter', [
            'dateFilter' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);

        $this->selectedLogId = null;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'categoryFilter',
            'dateFilter',
            'selectedLogId',
        ]);

        $this->resetValidation();
        $this->resetPage();
    }

    public function toggleDetails(int $logId): void
    {
        ActivityLog::query()->findOrFail($logId);

        $this->selectedLogId = $this->selectedLogId === $logId
            ? null
            : $logId;
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

        $search = trim($this->search);

        $logs = ActivityLog::query()
            ->with('user:id,name,email,role')
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(
                        function (Builder $query) use ($search): void {
                            $query
                                ->where(
                                    'action',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'ip_address',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'entity_type',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'user',
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
                    $this->categoryFilter,
                    self::ACTION_CATEGORIES,
                    true
                ),
                fn (Builder $query) => $query->where(
                    'action',
                    'like',
                    "{$this->categoryFilter}.%"
                )
            )
            ->when(
                $this->dateFilter !== '',
                fn (Builder $query) => $query->whereDate(
                    'created_at',
                    $this->dateFilter
                )
            )
            ->latest()
            ->paginate(20);

        $categoryCounts = collect(self::ACTION_CATEGORIES)
            ->mapWithKeys(
                fn (string $category): array => [
                    $category => ActivityLog::query()
                        ->where(
                            'action',
                            'like',
                            "{$category}.%"
                        )
                        ->count(),
                ]
            )
            ->all();

        $metrics = [
            'total' => ActivityLog::query()->count(),

            'today' => ActivityLog::query()
                ->whereDate('created_at', today())
                ->count(),

            'last_24_hours' => ActivityLog::query()
                ->where(
                    'created_at',
                    '>=',
                    now()->subDay()
                )
                ->count(),

            'users_involved' => ActivityLog::query()
                ->whereNotNull('user_id')
                ->distinct()
                ->count('user_id'),
        ];

        return view(
            'livewire.admin.activity-log-viewer',
            [
                'logs' => $logs,
                'metrics' => $metrics,
                'categoryOptions' => self::ACTION_CATEGORIES,
                'categoryCounts' => $categoryCounts,
            ]
        );
    }
}