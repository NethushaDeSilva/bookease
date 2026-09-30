<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Enums\BookingStatus;
use App\Enums\BusinessStatus;
use App\Models\Booking;
use App\Models\Business;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\DeleteUserForm;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    private function deleteThroughProfile(User $user): void
    {
        $this->actingAs($user);
        Livewire::test(DeleteUserForm::class)
            ->set('password', 'password')
            ->call('deleteUser')
            ->assertHasNoErrors()
            ->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull(User::find($user->id));
        $this->assertSoftDeleted($user);
    }

    public function test_all_roles_can_delete_their_own_account_and_revoke_credentials(): void
    {
        foreach (['customer', 'provider', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $email = $user->email;
            $token = $user->createToken('test')->accessToken;
            DB::table('sessions')->insert([
                'id' => 'other-session-'.$user->id, 'user_id' => $user->id,
                'payload' => '', 'last_activity' => time(),
            ]);
            DB::table('password_reset_tokens')->insert([
                'email' => $email, 'token' => 'old-reset-token', 'created_at' => now(),
            ]);

            $this->deleteThroughProfile($user);

            $archived = User::withTrashed()->findOrFail($user->id);
            $this->assertSame('Deleted user', $archived->name);
            $this->assertNotSame($email, $archived->email);
            $this->assertFalse(Hash::check('password', $archived->password));
            $this->assertNull($token->fresh());
            $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
            $this->assertDatabaseMissing('password_reset_tokens', ['email' => $email]);
            $this->post('/login', ['email' => $email, 'password' => 'password'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_provider_deletion_hides_services_and_preserves_other_customers_history(): void
    {
        $provider = User::factory()->provider()->create();
        $business = Business::factory()->for($provider, 'owner')->create();
        $service = Service::factory()->for($business)->create(['is_active' => true]);
        $pending = Booking::factory()->create(['service_id' => $service->id]);
        $completed = Booking::factory()->completed()->create(['service_id' => $service->id]);
        $review = Review::factory()->create([
            'booking_id' => $completed->id, 'customer_id' => $completed->customer_id,
        ]);
        $unrelated = Booking::factory()->confirmed()->create();

        $this->deleteThroughProfile($provider);

        $this->assertSame(BusinessStatus::Suspended, $business->fresh()->status);
        $this->assertSame('Deleted user', $business->fresh()->owner->name);
        $this->assertFalse($service->fresh()->is_active);
        $this->assertFalse($pending->slot->fresh()->is_active);
        $this->assertFalse(Service::visibleToCustomers()->whereKey($service->id)->exists());
        $this->assertSame(BookingStatus::Cancelled, $pending->fresh()->status);
        $this->assertSame(BookingStatus::Completed, $completed->fresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $unrelated->fresh()->status);
        $this->assertNotNull($review->fresh());
        $this->assertNotNull(User::find($pending->customer_id));
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $pending->id, 'new_status' => 'cancelled',
            'reason' => 'Provider account deleted.',
        ]);
        $this->actingAs($pending->customer)->get(route('customer.bookings.index'))->assertOk();
    }

    public function test_customer_with_bookings_and_reviews_can_delete_and_register_again(): void
    {
        $customer = User::factory()->customer()->create();
        $email = $customer->email;
        $pending = Booking::factory()->confirmed()->create(['customer_id' => $customer->id]);
        $completed = Booking::factory()->completed()->create(['customer_id' => $customer->id]);
        $review = Review::factory()->create([
            'booking_id' => $completed->id, 'customer_id' => $customer->id,
        ]);
        $this->deleteThroughProfile($customer);

        $this->assertSame(BookingStatus::Cancelled, $pending->fresh()->status);
        $this->assertSame(BookingStatus::Completed, $completed->fresh()->status);
        $this->assertSame('Deleted user', $review->fresh()->customer->name);
        $this->assertSame('Deleted user', $completed->fresh()->customer->name);
        $this->assertSame(0, $pending->slot->activeBookings()->count());
        $replacement = app(CreateNewUser::class)->create([
            'name' => 'New account', 'email' => $email,
            'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!',
            'role' => 'customer', 'terms' => true,
        ]);
        $this->assertNotSame($customer->id, $replacement->id);
        $this->assertSame(0, $replacement->bookings()->count());
    }

    public function test_wrong_password_preserves_account_and_related_records(): void
    {
        $user = User::factory()->provider()->create();
        $business = Business::factory()->for($user, 'owner')->create();
        $token = $user->createToken('test')->accessToken;
        $this->actingAs($user);
        Livewire::test(DeleteUserForm::class)
            ->set('password', 'wrong-password')->call('deleteUser')
            ->assertHasErrors(['password']);
        $this->assertNotNull(User::find($user->id));
        $this->assertSame($business->status, $business->fresh()->status);
        $this->assertNotNull($token->fresh());
    }
}
