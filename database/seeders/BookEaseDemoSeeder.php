<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\BusinessStatus;
use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Business;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BookEaseDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@bookease.test')->exists()) {
            $this->command?->warn('BookEase demonstration data already exists.');

            return;
        }

        DB::transaction(function (): void {
            $password = Hash::make('Password123!');

            $admin = User::factory()
                ->admin()
                ->create([
                    'name' => 'BookEase Administrator',
                    'email' => 'admin@bookease.test',
                    'email_verified_at' => now(),
                    'password' => $password,
                ]);

            $provider = User::factory()
                ->provider()
                ->create([
                    'name' => 'Demo Service Provider',
                    'email' => 'provider@bookease.test',
                    'email_verified_at' => now(),
                    'password' => $password,
                ]);

            $customer = User::factory()
                ->customer()
                ->create([
                    'name' => 'Demo Customer',
                    'email' => 'customer@bookease.test',
                    'email_verified_at' => now(),
                    'password' => $password,
                ]);

            $additionalCustomers = User::factory()
                ->customer()
                ->count(4)
                ->create();

            $business = Business::factory()
                ->for($provider, 'owner')
                ->create([
                    'name' => 'Colombo Learning Centre',
                    'slug' => 'colombo-learning-centre',
                    'description' => 'Professional tutoring and consultation services.',
                    'phone' => '0771234567',
                    'email' => 'provider@bookease.test',
                    'address' => 'Colombo, Sri Lanka',
                    'status' => BusinessStatus::Active->value,
                ]);

            $serviceInformation = [
                [
                    'name' => 'Mathematics Tutoring',
                    'description' => 'Individual mathematics lessons for school students.',
                    'duration_minutes' => 60,
                    'price' => 2500,
                    'is_active' => true,
                ],
                [
                    'name' => 'Programming Consultation',
                    'description' => 'Individual software-development consultation.',
                    'duration_minutes' => 90,
                    'price' => 4000,
                    'is_active' => true,
                ],
                [
                    'name' => 'English Language Tutoring',
                    'description' => 'English language lessons for students and professionals.',
                    'duration_minutes' => 60,
                    'price' => 2200,
                    'is_active' => true,
                ],
            ];

            $services = collect();

            foreach ($serviceInformation as $information) {
                $services->push(
                    Service::factory()
                        ->for($business)
                        ->create($information)
                );
            }

            $futureSlots = collect();

            foreach ($services as $service) {
                foreach (range(1, 7) as $day) {
                    foreach ([9, 11, 14] as $hour) {
                        $startsAt = now()
                            ->addDays($day)
                            ->setTime($hour, 0);

                        $futureSlots->push(
                            AvailabilitySlot::factory()
                                ->for($service)
                                ->create([
                                    'starts_at' => $startsAt,
                                    'ends_at' => $startsAt
                                        ->copy()
                                        ->addMinutes($service->duration_minutes),
                                    'capacity' => 1,
                                    'is_active' => true,
                                ])
                        );
                    }
                }
            }

            $pastStart = now()->subDays(3)->setTime(10, 0);
            $firstService = $services->first();

            $pastSlot = AvailabilitySlot::factory()
                ->for($firstService)
                ->create([
                    'starts_at' => $pastStart,
                    'ends_at' => $pastStart
                        ->copy()
                        ->addMinutes($firstService->duration_minutes),
                    'capacity' => 1,
                    'is_active' => true,
                ]);

            $pendingBooking = Booking::factory()->create([
                'customer_id' => $customer->id,
                'service_id' => $futureSlots[0]->service_id,
                'slot_id' => $futureSlots[0]->id,
                'status' => BookingStatus::Pending->value,
                'price' => $futureSlots[0]->service->price,
                'notes' => 'Please provide revision exercises.',
            ]);

            $confirmedBooking = Booking::factory()
                ->confirmed()
                ->create([
                    'customer_id' => $additionalCustomers[0]->id,
                    'service_id' => $futureSlots[1]->service_id,
                    'slot_id' => $futureSlots[1]->id,
                    'price' => $futureSlots[1]->service->price,
                ]);

            $cancelledBooking = Booking::factory()
                ->cancelled()
                ->create([
                    'customer_id' => $additionalCustomers[1]->id,
                    'service_id' => $futureSlots[2]->service_id,
                    'slot_id' => $futureSlots[2]->id,
                    'price' => $futureSlots[2]->service->price,
                    'cancellation_reason' => 'Customer schedule changed.',
                ]);

            $completedBooking = Booking::factory()
                ->completed()
                ->create([
                    'customer_id' => $customer->id,
                    'service_id' => $firstService->id,
                    'slot_id' => $pastSlot->id,
                    'price' => $firstService->price,
                ]);

            $recordHistory = function (
                Booking $booking,
                ?BookingStatus $oldStatus,
                BookingStatus $newStatus,
                User $changedBy,
                string $reason
            ): void {
                BookingStatusHistory::factory()->create([
                    'booking_id' => $booking->id,
                    'changed_by' => $changedBy->id,
                    'old_status' => $oldStatus?->value,
                    'new_status' => $newStatus->value,
                    'reason' => $reason,
                ]);
            };

            $recordHistory(
                $pendingBooking,
                null,
                BookingStatus::Pending,
                $customer,
                'Booking created by customer.'
            );

            $recordHistory(
                $confirmedBooking,
                null,
                BookingStatus::Pending,
                $additionalCustomers[0],
                'Booking created by customer.'
            );

            $recordHistory(
                $confirmedBooking,
                BookingStatus::Pending,
                BookingStatus::Confirmed,
                $provider,
                'Booking confirmed by provider.'
            );

            $recordHistory(
                $cancelledBooking,
                null,
                BookingStatus::Pending,
                $additionalCustomers[1],
                'Booking created by customer.'
            );

            $recordHistory(
                $cancelledBooking,
                BookingStatus::Pending,
                BookingStatus::Cancelled,
                $additionalCustomers[1],
                'Booking cancelled by customer.'
            );

            $recordHistory(
                $completedBooking,
                null,
                BookingStatus::Pending,
                $customer,
                'Booking created by customer.'
            );

            $recordHistory(
                $completedBooking,
                BookingStatus::Pending,
                BookingStatus::Confirmed,
                $provider,
                'Booking confirmed by provider.'
            );

            $recordHistory(
                $completedBooking,
                BookingStatus::Confirmed,
                BookingStatus::Completed,
                $provider,
                'Service completed successfully.'
            );

            Review::factory()->create([
                'booking_id' => $completedBooking->id,
                'customer_id' => $customer->id,
                'rating' => 5,
                'comment' => 'Excellent service and clear explanations.',
            ]);

            ActivityLog::factory()->create([
                'user_id' => $provider->id,
                'action' => 'business.created',
                'entity_type' => $business->getMorphClass(),
                'entity_id' => $business->id,
                'metadata' => [
                    'business_name' => $business->name,
                ],
            ]);

            ActivityLog::factory()->create([
                'user_id' => $customer->id,
                'action' => 'booking.created',
                'entity_type' => $pendingBooking->getMorphClass(),
                'entity_id' => $pendingBooking->id,
                'metadata' => [
                    'booking_reference' => $pendingBooking->booking_reference,
                ],
            ]);

            ActivityLog::factory()->create([
                'user_id' => $provider->id,
                'action' => 'booking.confirmed',
                'entity_type' => $confirmedBooking->getMorphClass(),
                'entity_id' => $confirmedBooking->id,
                'metadata' => [
                    'booking_reference' => $confirmedBooking->booking_reference,
                ],
            ]);

            ActivityLog::factory()->create([
                'user_id' => $admin->id,
                'action' => 'demo_data.created',
                'entity_type' => null,
                'entity_id' => null,
                'metadata' => [
                    'environment' => app()->environment(),
                ],
            ]);
        });

        $this->command?->info('BookEase demonstration data created successfully.');
    }
}