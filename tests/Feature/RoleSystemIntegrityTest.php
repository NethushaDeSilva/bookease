<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Livewire\Customer\ServiceDetails;
use App\Livewire\Provider\AvailabilityManager;
use App\Livewire\Provider\ServiceManager;
use App\Models\AvailabilitySlot;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class RoleSystemIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_open_and_filter_a_visible_services_details(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $service = Service::factory()->for($business)->create(['name' => 'Math tutoring']);
        $slot = AvailabilitySlot::factory()->for($service)->create();
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer);
        $this->get(route('customer.services.index'))->assertOk()->assertSee('Math tutoring');
        $this->get(route('customer.services.show', $service))->assertOk()->assertSee('Math tutoring');

        Livewire::test(ServiceDetails::class, ['service' => $service])
            ->assertOk()
            ->set('dateFilter', $slot->starts_at->toDateString())
            ->assertHasNoErrors()
            ->assertOk()
            ->call('clearDateFilter')
            ->assertOk();
    }

    public function test_customer_cannot_open_a_pending_business_service(): void
    {
        $business = Business::factory()->pending()->create();
        $service = Service::factory()->for($business)->create();

        $this->actingAs(User::factory()->customer()->create())
            ->get(route('customer.services.show', $service))
            ->assertForbidden();
    }

    public function test_provider_can_open_their_service_editor(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $service = Service::factory()->for($business)->create();

        $this->actingAs($provider)->get(route('provider.services.index'))->assertOk();

        Livewire::test(ServiceManager::class)
            ->call('edit', $service->id)
            ->assertHasNoErrors()
            ->assertSet('serviceId', $service->id)
            ->assertSet('name', $service->name);
    }

    public function test_provider_cannot_tamper_business_id_to_read_another_providers_services(): void
    {
        $owner = User::factory()->provider()->create();
        Business::factory()->for($owner, 'owner')->create();

        $otherOwner = User::factory()->provider()->create();
        $otherBusiness = Business::factory()->for($otherOwner, 'owner')->create();
        Service::factory()->for($otherBusiness)->create(['name' => 'Rival Secret Service']);

        $this->actingAs($owner);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(ServiceManager::class)->set('businessId', $otherBusiness->id);
    }

    public function test_provider_cannot_tamper_business_id_to_read_another_providers_availability(): void
    {
        $owner = User::factory()->provider()->create();
        Business::factory()->for($owner, 'owner')->create();

        $otherOwner = User::factory()->provider()->create();
        $otherBusiness = Business::factory()->for($otherOwner, 'owner')->create();
        $otherService = Service::factory()->for($otherBusiness)->create();
        AvailabilitySlot::factory()->for($otherService)->create();

        $this->actingAs($owner);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(AvailabilityManager::class)->set('businessId', $otherBusiness->id);
    }

    public function test_suspended_providers_services_are_not_visible_or_bookable(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $service = Service::factory()->for($business)->create();
        $slot = AvailabilitySlot::factory()->for($service)->create();

        $customer = User::factory()->customer()->create();

        $this->assertTrue($customer->can('view', $service));
        $this->assertNotNull(Service::query()->visibleToCustomers()->find($service->id));

        $provider->forceFill(['status' => UserStatus::Suspended])->save();

        $this->assertNull(
            Service::query()->visibleToCustomers()->find($service->id),
            "A suspended provider's service must not be customer-visible."
        );

        $this->assertFalse($customer->can('view', $service->fresh()));

        Sanctum::actingAs($customer, ['services:read']);
        $this->getJson('/api/v1/services/'.$service->id)->assertNotFound();
        $this->getJson('/api/v1/services')->assertJsonCount(0, 'data');

        $this->actingAs($customer)
            ->get(route('customer.services.show', $service))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('customer.bookings.create', [$service, $slot]))
            ->assertForbidden();
    }
}
