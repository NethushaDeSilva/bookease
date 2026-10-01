<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Livewire\Customer\BookingCreator;
use App\Livewire\Customer\ReviewCreator;
use App\Livewire\Provider\BookingManager;
use App\Livewire\Provider\ServiceManager;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceBookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_books_provider_completes_and_customer_reviews(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $service = Service::factory()->for($business)->create();
        $slot = AvailabilitySlot::factory()->for($service)->create(['capacity' => 1]);
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer);
        $this->get(route('customer.services.show', $service))->assertOk();
        $this->get(route('customer.bookings.create', [$service, $slot]))->assertOk();
        Livewire::test(BookingCreator::class, ['service' => $service, 'slot' => $slot])
            ->set('notes', 'Please focus on algebra.')
            ->call('createBooking')->assertHasNoErrors()->assertSet('bookingCreated', true);

        $booking = Booking::sole();
        $this->assertSame($customer->id, $booking->customer_id);
        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertFalse($slot->fresh()->isAvailable());
        $this->get(route('customer.bookings.index'))->assertOk()->assertSee($booking->booking_reference);

        $this->actingAs($provider);
        $this->get(route('provider.bookings.index'))->assertOk()->assertSee($booking->booking_reference);
        Livewire::test(BookingManager::class)
            ->call('confirmBooking', $booking->id)->assertHasNoErrors()
            ->call('completeBooking', $booking->id)->assertHasNoErrors();
        $this->assertSame(BookingStatus::Completed, $booking->fresh()->status);

        $this->actingAs($customer);
        $this->get(route('customer.bookings.review', $booking))->assertOk();
        Livewire::test(ReviewCreator::class, ['booking' => $booking->fresh()])
            ->set('rating', 5)->set('comment', 'Helpful session.')
            ->call('createReview')->assertHasNoErrors()
            ->assertRedirect(route('customer.bookings.index'));
        $this->assertDatabaseHas('reviews', ['booking_id' => $booking->id, 'customer_id' => $customer->id, 'rating' => 5]);
    }

    public function test_provider_list_and_edit_are_limited_to_own_services(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $own = Service::factory()->for($business)->create(['name' => 'Owned tutoring']);
        $other = Service::factory()->create(['name' => 'Other provider tutoring']);

        $this->actingAs($provider)->get(route('provider.services.index'))
            ->assertOk()->assertSee($own->name)->assertDontSee($other->name);
        $this->expectException(ModelNotFoundException::class);
        Livewire::test(ServiceManager::class)->call('edit', $other->id);
    }

    public function test_customer_catalogue_excludes_inactive_and_unapproved_services(): void
    {
        $visible = Service::factory()->create(['name' => 'Open tutoring']);
        $inactive = Service::factory()->inactive()->create(['name' => 'Inactive tutoring']);
        $pending = Service::factory()->for(Business::factory()->pending())->create(['name' => 'Pending tutoring']);
        $suspended = Service::factory()->for(Business::factory()->suspended())->create(['name' => 'Suspended tutoring']);

        $this->actingAs(User::factory()->customer()->create())
            ->get(route('customer.services.index'))->assertOk()->assertSee($visible->name)
            ->assertDontSee($inactive->name)->assertDontSee($pending->name)->assertDontSee($suspended->name);
        foreach ([$inactive, $pending, $suspended] as $service) {
            $this->get(route('customer.services.show', $service))->assertForbidden();
        }
    }
}
