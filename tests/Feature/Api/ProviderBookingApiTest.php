<?php

namespace Tests\Feature\Api;

use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProviderBookingApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A provider sees only their business bookings.
     */
    public function test_provider_only_sees_own_business_bookings(): void
    {
        $provider = User::factory()
            ->provider()
            ->create();

        $otherProvider = User::factory()
            ->provider()
            ->create();

        $providerBusiness = Business::factory()->create([
            'owner_id' => $provider->id,
        ]);

        $otherBusiness = Business::factory()->create([
            'owner_id' => $otherProvider->id,
        ]);

        $providerService = Service::factory()->create([
            'business_id' => $providerBusiness->id,
        ]);

        $otherService = Service::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $firstStart = now()
            ->addDays(3)
            ->startOfHour();

        $secondStart = now()
            ->addDays(4)
            ->startOfHour();

        $providerSlot = AvailabilitySlot::factory()->create([
            'service_id' => $providerService->id,
            'starts_at' => $firstStart,
            'ends_at' => $firstStart->copy()->addHour(),
        ]);

        $otherSlot = AvailabilitySlot::factory()->create([
            'service_id' => $otherService->id,
            'starts_at' => $secondStart,
            'ends_at' => $secondStart->copy()->addHour(),
        ]);

        $providerBooking = Booking::factory()->create([
            'service_id' => $providerService->id,
            'slot_id' => $providerSlot->id,
            'price' => $providerService->price,
        ]);

        $otherBooking = Booking::factory()->create([
            'service_id' => $otherService->id,
            'slot_id' => $otherSlot->id,
            'price' => $otherService->price,
        ]);

        Sanctum::actingAs(
            $provider,
            ['provider:manage-bookings']
        );

        $response = $this->getJson(
            '/api/v1/provider/bookings'
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'booking_reference' =>
                $providerBooking->booking_reference,
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
     * A provider can confirm their own pending booking.
     */
    public function test_provider_can_confirm_pending_booking(): void
    {
        $provider = User::factory()
            ->provider()
            ->create();

        $business = Business::factory()->create([
            'owner_id' => $provider->id,
        ]);

        $service = Service::factory()->create([
            'business_id' => $business->id,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
        ]);

        $booking = Booking::factory()->create([
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'pending',
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $provider,
            ['provider:manage-bookings']
        );

        $response = $this->patchJson(
            "/api/v1/provider/bookings/{$booking->id}/status",
            [
                'status' => 'confirmed',
                'reason' =>
                'The requested appointment is available.',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'confirmed'
            )
            ->assertJsonPath(
                'message',
                'The booking status was updated successfully.'
            );

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas(
            'booking_status_histories',
            [
                'booking_id' => $booking->id,
                'changed_by' => $provider->id,
                'old_status' => 'pending',
                'new_status' => 'confirmed',
                'reason' =>
                'The requested appointment is available.',
            ]
        );
    }

    /**
     * A provider cannot manage another provider's booking.
     */
    public function test_provider_cannot_manage_another_providers_booking(): void
    {
        $provider = User::factory()
            ->provider()
            ->create();

        $otherProvider = User::factory()
            ->provider()
            ->create();

        $otherBusiness = Business::factory()->create([
            'owner_id' => $otherProvider->id,
        ]);

        $otherService = Service::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $otherSlot = AvailabilitySlot::factory()->create([
            'service_id' => $otherService->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
        ]);

        $otherBooking = Booking::factory()->create([
            'service_id' => $otherService->id,
            'slot_id' => $otherSlot->id,
            'status' => 'pending',
            'price' => $otherService->price,
        ]);

        Sanctum::actingAs(
            $provider,
            ['provider:manage-bookings']
        );

        $response = $this->patchJson(
            "/api/v1/provider/bookings/{$otherBooking->id}/status",
            [
                'status' => 'confirmed',
                'reason' =>
                'Unauthorized confirmation attempt.',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('bookings', [
            'id' => $otherBooking->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing(
            'booking_status_histories',
            [
                'booking_id' => $otherBooking->id,
                'changed_by' => $provider->id,
                'new_status' => 'confirmed',
            ]
        );
    }

    /**
     * A pending booking cannot be completed directly.
     */
    public function test_provider_cannot_complete_pending_booking(): void
    {
        $provider = User::factory()
            ->provider()
            ->create();

        $business = Business::factory()->create([
            'owner_id' => $provider->id,
        ]);

        $service = Service::factory()->create([
            'business_id' => $business->id,
        ]);

        $startsAt = now()
            ->addDays(3)
            ->startOfHour();

        $slot = AvailabilitySlot::factory()->create([
            'service_id' => $service->id,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
        ]);

        $booking = Booking::factory()->create([
            'service_id' => $service->id,
            'slot_id' => $slot->id,
            'status' => 'pending',
            'price' => $service->price,
        ]);

        Sanctum::actingAs(
            $provider,
            ['provider:manage-bookings']
        );

        $response = $this->patchJson(
            "/api/v1/provider/bookings/{$booking->id}/status",
            [
                'status' => 'completed',
                'reason' =>
                'Invalid direct completion attempt.',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing(
            'booking_status_histories',
            [
                'booking_id' => $booking->id,
                'new_status' => 'completed',
            ]
        );
    }
}
