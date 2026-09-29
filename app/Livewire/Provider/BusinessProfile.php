<?php

namespace App\Livewire\Provider;

use App\Enums\BusinessStatus;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class BusinessProfile extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public ?int $businessId = null;

    public string $name = '';

    public string $description = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $businessStatus = '';

    public function mount(): void
    {
        $business = $this->authenticatedUser()->business()->first();

        if (! $business) {
            return;
        }

        $this->authorize('update', $business);

        $this->businessId = $business->id;
        $this->name = $business->name;
        $this->description = $business->description ?? '';
        $this->phone = $business->phone ?? '';
        $this->email = $business->email ?? '';
        $this->address = $business->address ?? '';
        $this->businessStatus = $business->status->value;
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $user = $this->authenticatedUser();

        $business = DB::transaction(function () use ($validated, $user): Business {
            $data = [
                'name' => $validated['name'],
                'slug' => $this->createUniqueSlug($validated['name']),
                'description' => $validated['description'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'email' => $validated['email'] ?: null,
                'address' => $validated['address'] ?: null,
            ];

            if ($this->businessId) {
                $business = Business::query()->findOrFail(
                    $this->businessId
                );

                $this->authorize('update', $business);

                $business->update($data);

                $action = 'business.updated';
            } else {
                $this->authorize('create', Business::class);

                $business = $user
                    ->business()
                    ->create(array_merge($data, [
                        'status' => BusinessStatus::Pending->value,
                    ]));

                $action = 'business.created';
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'entity_type' => $business->getMorphClass(),
                'entity_id' => $business->id,
                'metadata' => [
                    'business_name' => $business->name,
                    'status' => $business->status->value,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $business;
        });

        $this->businessId = $business->id;
        $this->businessStatus = $business->status->value;

        session()->flash(
            'success',
            'Your business profile was saved successfully.'
        );
    }

    private function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    private function createUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'business';
        }

        $slug = $baseSlug;
        $number = 2;

        while (
            Business::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $this->businessId,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $this->businessId
                    )
                )
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$number;
            $number++;
        }

        return $slug;
    }

    public function render(): View
    {
        return view('livewire.provider.business-profile');
    }
}