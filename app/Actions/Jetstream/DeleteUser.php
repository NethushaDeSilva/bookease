<?php

namespace App\Actions\Jetstream;

use App\Enums\BookingStatus;
use App\Enums\BusinessStatus;
use App\Enums\UserStatus;
use App\Models\AvailabilitySlot;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Laravel\Jetstream\Features;

class DeleteUser implements DeletesUsers
{
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $account = User::query()->lockForUpdate()->findOrFail($user->id);
            $businessIds = $account->business()->withTrashed()->pluck('id');
            $serviceIds = Service::withTrashed()->whereIn('business_id', $businessIds)->pluck('id');

            $account->business()->withTrashed()->update(['status' => BusinessStatus::Suspended->value]);
            Service::withTrashed()->whereIn('id', $serviceIds)->update(['is_active' => false]);
            AvailabilitySlot::whereIn('service_id', $serviceIds)->update(['is_active' => false]);

            $bookings = Booking::query()
                ->whereIn('status', BookingStatus::activeValues())
                ->where(fn ($query) => $query->where('customer_id', $account->id)
                    ->orWhereIn('service_id', $serviceIds))
                ->orderBy('id')->lockForUpdate()->get();

            foreach ($bookings as $booking) {
                $oldStatus = $booking->status;
                $reason = $booking->customer_id === $account->id
                    ? 'Customer account deleted.' : 'Provider account deleted.';
                $booking->update([
                    'status' => BookingStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                ]);
                $booking->statusHistories()->create([
                    'changed_by' => $account->id,
                    'old_status' => $oldStatus,
                    'new_status' => BookingStatus::Cancelled,
                    'reason' => $reason,
                ]);
            }

            $account->tokens()->delete();
            DB::table('sessions')->where('user_id', $account->id)->delete();
            DB::table('passkeys')->where('user_id', $account->id)->delete();
            DB::table('password_reset_tokens')->where('email', $account->email)->delete();

            // Preserve the reference ID for shared history, but remove account identity.
            $account->forceFill([
                'name' => 'Deleted user',
                'email' => 'deleted-'.Str::uuid().'@example.invalid',
                'password' => Str::random(64),
                'status' => UserStatus::Suspended,
                'email_verified_at' => null,
                'remember_token' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'profile_photo_path' => null,
            ])->save();
            $account->delete();
        }, 3);

        if (Features::managesProfilePhotos() && $user->profile_photo_path) {
            Storage::disk(config('jetstream.profile_photo_disk', 'public'))
                ->delete($user->profile_photo_path);
        }
    }
}
