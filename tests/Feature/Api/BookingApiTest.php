<?php

namespace Tests\Feature\Api;

use App\Models\AvailabilitySlot;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\Booking;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A customer can create a valid booking.
     */
    public function test_customer_can_create_valid_booking(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create([
            'price' => 2500,
            'is_active' => true,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'capacity' => 1,
            'is_active' => true,
        ]);

        Sanctum::actingAs(
            $customer,
            ['bookings:create']
        );

        $response = $this->postJson(
            '/api/v1/bookings',
            [
                'slot_id' => $slot->id,
                'notes' => 'API feature test booking.',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.status',
                'pending'
            )
            ->assertJsonPath(
                'data.notes',
                'API feature test booking.'
            )
            ->assertJsonPath(
                'data.service.id',
                $service->id
            )
            ->assertJsonPath(
                'data.slot.id',
                $slot->id
            )
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'booking_reference',
                    'status',
                    'price',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'pending',
            'notes' => 'API feature test booking.',
        ]);

        $this->assertDatabaseHas(
            'booking_status_histories',
            [
                'changed_by' => $customer->id,
                'old_status' => null,
                'new_status' => 'pending',
            ]
        );
    }

    /**
     * A customer cannot book a slot in the past.
     */
    public function test_customer_cannot_book_past_slot(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create([
            'is_active' => true,
        ]);

        $startsAt = now()
            ->subDay()
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'capacity' => 1,
            'is_active' => true,
        ]);

        Sanctum::actingAs(
            $customer,
            ['bookings:create']
        );

        $response = $this->postJson(
            '/api/v1/bookings',
            [
                'slot_id' => $slot->id,
                'notes' => 'Attempt to book a past slot.',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'slot_id',
            ]);

        $this->assertDatabaseMissing('bookings', [
            'customer_id' => $customer->id,
            'slot_id' => $slot->id,
        ]);
    }

    /**
     * A customer cannot book a slot that is already full.
     */
    public function test_customer_cannot_book_full_slot(): void
    {
        $existingCustomer = User::factory()
            ->customer()
            ->create();

        $newCustomer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create([
            'is_active' => true,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'capacity' => 1,
            'is_active' => true,
        ]);

        Booking::factory()->create([
            'customer_id' => $existingCustomer->id,
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'pending',
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $newCustomer,
            ['bookings:create']
        );

        $response = $this->postJson(
            '/api/v1/bookings',
            [
                'slot_id' => $slot->id,
                'notes' => 'Trying to book a full slot.',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'slot_id',
            ]);

        $this->assertDatabaseMissing('bookings', [
            'customer_id' => $newCustomer->id,
            'slot_id' => $slot->id,
        ]);

        $this->assertSame(
            1,
            Booking::query()
                ->where('slot_id', $slot->id)
                ->count()
        );
    }

    /**
     * A customer cannot create duplicate active bookings.
     */
    public function test_customer_cannot_duplicate_active_booking(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create([
            'is_active' => true,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'capacity' => 2,
            'is_active' => true,
        ]);

        Booking::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'confirmed',
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $customer,
            ['bookings:create']
        );

        $response = $this->postJson(
            '/api/v1/bookings',
            [
                'slot_id' => $slot->id,
                'notes' => 'Duplicate booking attempt.',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'slot_id',
            ]);

        $this->assertSame(
            1,
            Booking::query()
                ->where('customer_id', $customer->id)
                ->where('slot_id', $slot->id)
                ->count()
        );
    }

    /**
     * A customer can only retrieve their own bookings.
     */
    public function test_customer_only_sees_own_bookings(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        $otherCustomer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create();

        $firstStart = now()
            ->addDays(3)
            ->startOfHour();

        $secondStart = now()
            ->addDays(4)
            ->startOfHour();

        $customerSlot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $firstStart,
            'ends_at' => $firstStart->copy()->addHour(),
        ]);

        $otherSlot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $secondStart,
            'ends_at' => $secondStart->copy()->addHour(),
        ]);

        $customerBooking = Booking::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'slot_id' => $customerSlot->id,
            'price' => $service->price,
        ]);

        $otherBooking = Booking::factory()->create([
            'customer_id' => $otherCustomer->id,
            'service_id' => $service->id,
            'slot_id' => $otherSlot->id,
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $customer,
            ['bookings:read']
        );

        $response = $this->getJson(
            '/api/v1/bookings'
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'booking_reference' =>
                $customerBooking->booking_reference,
            ])
            ->assertJsonMissing([
                'booking_reference' =>
                $otherBooking->booking_reference,
            ]);

        $this->assertCount(
            1,
            $response->json('data')
        );
    }

    /**
     * A customer can cancel an eligible booking.
     */
    public function test_customer_can_cancel_eligible_booking(): void
    {
        $customer = User::factory()
            ->customer()
            ->create();

        $service = Service::factory()->create();

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
            'is_active' => true,
        ]);

        $booking = Booking::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'pending',
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $customer,
            ['bookings:cancel']
        );

        $response = $this->patchJson(
            "/api/v1/bookings/{$booking->id}/cancel",
            [
                'reason' => 'My schedule has changed.',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'cancelled'
            )
            ->assertJsonPath(
                'data.cancellation_reason',
                'My schedule has changed.'
            )
            ->assertJsonPath(
                'message',
                'The booking was cancelled successfully.'
            );

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'customer_id' => $customer->id,
            'status' => 'cancelled',
            'cancellation_reason' =>
            'My schedule has changed.',
        ]);

        $this->assertDatabaseHas(
            'booking_status_histories',
            [
                'booking_id' => $booking->id,
                'changed_by' => $customer->id,
                'old_status' => 'pending',
                'new_status' => 'cancelled',
                'reason' => 'My schedule has changed.',
            ]
        );
    }

    
}
