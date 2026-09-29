<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Livewire\Customer\BookingList;
use App\Livewire\Customer\ReviewCreator;
use App\Livewire\Provider\BusinessProfile;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountRoleTest extends TestCase
{
    use RefreshDatabase;

    public static function registrationRoles(): array
    {
        return [['customer', 'customer.services.index'], ['provider', 'provider.business.profile']];
    }

    #[DataProvider('registrationRoles')]
    public function test_selected_role_is_saved_and_routes_correctly_after_verification(string $role, string $destination): void
    {
        Notification::fake();

        $this->post('/register', $this->registrationInput($role))
            ->assertSessionHasNoErrors()->assertRedirect('/dashboard');

        $user = User::where('email', 'role@example.test')->firstOrFail();
        $this->assertSame($role, $user->role->value);
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get('/dashboard')->assertRedirect(route('verification.notice'));

        $this->get(URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]))->assertRedirect('/dashboard');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get('/dashboard')->assertRedirect(route($destination));
        $this->get(route($destination))->assertOk();
        $this->get('/admin/users')->assertForbidden();
    }

    public static function invalidRoles(): array
    {
        return [['admin'], ['owner'], [''], [null], [['customer', 'provider']]];
    }

    #[DataProvider('invalidRoles')]
    public function test_registration_rejects_invalid_or_multiple_roles(mixed $role): void
    {
        Notification::fake();
        $this->post('/register', $this->registrationInput($role))->assertSessionHasErrors('role');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
        Notification::assertNothingSent();
    }

    public function test_registration_requires_a_role(): void
    {
        $input = $this->registrationInput('customer');
        unset($input['role']);
        $this->post('/register', $input)->assertSessionHasErrors('role');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_role_selection_survives_validation_errors_and_is_exclusive(): void
    {
        $this->from('/register')->post('/register', [
            ...$this->registrationInput('provider'),
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors('password')->assertSessionHasInput('role', 'provider');

        $html = $this->get('/register')->assertOk()->getContent();
        $document = new \DOMDocument;
        @$document->loadHTML($html);
        $xpath = new \DOMXPath($document);
        $this->assertSame(2, $xpath->query('//input[@type="radio" and @name="role"]')->length);
        $checked = $xpath->query('//input[@name="role" and @checked]');
        $this->assertSame(1, $checked->length);
        $this->assertSame('provider', $checked->item(0)->getAttribute('value'));
    }

    public function test_customer_can_become_provider_and_submit_business_for_approval(): void
    {
        $user = User::factory()->customer()->create();
        $other = User::factory()->customer()->create();
        $booking = Booking::factory()->create(['customer_id' => $user->id]);
        $user->createToken('existing', ['bookings:read']);

        $this->actingAs($user)->get('/user/profile')->assertOk()->assertSee('Become a provider');
        $this->post(route('profile.become-provider'), ['user_id' => $other->id, 'role' => 'admin'])
            ->assertRedirect(route('provider.business.profile'));

        $this->assertSame(UserRole::Provider, $user->fresh()->role);
        $this->assertSame(UserRole::Customer, $other->fresh()->role);
        $this->assertSame($user->id, $booking->fresh()->customer_id);
        $this->assertSame(1, $user->tokens()->count());
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id, 'entity_id' => $user->id, 'action' => 'user.became_provider',
        ]);
        $this->assertDatabaseCount('businesses', 1); // Only the existing appointment's business.

        $this->actingAs($user->fresh())->get('/dashboard')->assertRedirect(route('provider.business.profile'));
        Livewire::test(BusinessProfile::class)
            ->set('name', 'My new business')->set('email', 'business@example.test')
            ->call('save')->assertHasNoErrors();
        $this->assertDatabaseHas('businesses', ['owner_id' => $user->id, 'status' => 'pending']);
        $this->get('/dashboard')->assertRedirect(route('provider.services.index'));
        $this->get('/provider/services')->assertOk();
        $this->get('/user/profile')->assertOk()->assertSee('Service provider')->assertDontSee('Become a provider');
    }

    public function test_repeated_conversion_is_safe_and_does_not_duplicate_audit_records(): void
    {
        $user = User::factory()->customer()->create();
        $this->actingAs($user)->post(route('profile.become-provider'))->assertRedirect();
        $this->actingAs($user->fresh())->post(route('profile.become-provider'))->assertRedirect();
        $this->assertSame(1, ActivityLog::where('action', 'user.became_provider')->count());
    }

    public function test_guests_unverified_suspended_and_admin_users_cannot_convert(): void
    {
        $this->postJson(route('profile.become-provider'))->assertUnauthorized();
        $users = [
            User::factory()->unverified()->create(),
            User::factory()->suspended()->create(),
            User::factory()->admin()->create(),
        ];
        foreach ($users as $user) {
            $role = $user->role;
            $this->actingAs($user)->postJson(route('profile.become-provider'))->assertForbidden();
            $this->assertSame($role, $user->fresh()->role);
        }
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_conversion_controls_are_unavailable_until_email_is_verified(): void
    {
        $this->actingAs(User::factory()->unverified()->create())->get('/user/profile')
            ->assertOk()->assertSee('Verify your email before becoming a provider.')
            ->assertDontSee('action="'.route('profile.become-provider').'"', false);
    }

    public function test_converted_provider_can_manage_earlier_personal_appointments(): void
    {
        $user = User::factory()->customer()->create();
        $booking = Booking::factory()->create(['customer_id' => $user->id]);
        $booking->slot->update(['starts_at' => now()->addDays(3), 'ends_at' => now()->addDays(3)->addHour()]);
        $completed = Booking::factory()->completed()->create(['customer_id' => $user->id]);
        $otherBooking = Booking::factory()->create();

        $this->actingAs($user)->post(route('profile.become-provider'))->assertRedirect();
        $this->actingAs($user->fresh())->get(route('customer.bookings.index'))
            ->assertOk()->assertSee($booking->booking_reference)
            ->assertDontSee($otherBooking->booking_reference)
            ->assertDontSee('Browse services')->assertDontSee('View service');

        Livewire::test(BookingList::class)->call('startCancellation', $booking->id)
            ->set('cancellationReason', 'My schedule has changed.')
            ->call('confirmCancellation')->assertHasNoErrors();
        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);

        $this->get(route('customer.bookings.review', $completed))->assertOk();
        Livewire::test(ReviewCreator::class, ['booking' => $completed])
            ->set('rating', 5)->set('comment', 'Great appointment!')
            ->call('createReview')->assertRedirect(route('customer.bookings.index'));
        $this->assertDatabaseHas('reviews', ['booking_id' => $completed->id, 'customer_id' => $user->id]);
        $this->get(route('customer.bookings.review', $otherBooking))->assertForbidden();
    }

    public function test_personal_booking_api_remains_scoped_after_conversion(): void
    {
        $user = User::factory()->customer()->create();
        $own = Booking::factory()->create(['customer_id' => $user->id]);
        $this->actingAs($user)->post(route('profile.become-provider'))->assertRedirect();
        $user = $user->fresh();
        $business = Business::factory()->create(['owner_id' => $user->id]);
        $service = $business->services()->create(['name' => 'Provider service', 'duration_minutes' => 60, 'price' => 2500]);
        $received = Booking::factory()->create(['service_id' => $service->id]);

        Sanctum::actingAs($user, ['bookings:read', 'bookings:create']);
        $this->getJson('/api/v1/bookings')->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $own->id);
        $this->getJson('/api/v1/bookings/'.$own->id)->assertOk();
        $this->getJson('/api/v1/bookings/'.$received->id)->assertForbidden();
        $this->postJson('/api/v1/bookings', ['slot_id' => $own->slot_id])->assertForbidden();
    }

    private function registrationInput(mixed $role): array
    {
        return [
            'name' => 'Role Test',
            'email' => 'role@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => $role,
            'terms' => true,
        ];
    }
}
