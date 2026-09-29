<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BecomeProviderController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);

            abort_unless($user->isActive() && $user->hasVerifiedEmail(), 403);
            abort_unless($user->isCustomer() || $user->isProvider(), 403);

            // A repeated submission must not change the role or log the transition twice.
            if ($user->isProvider()) {
                return;
            }

            $user->role = UserRole::Provider;
            $user->save();

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'user.became_provider',
                'entity_type' => $user->getMorphClass(),
                'entity_id' => $user->id,
                'metadata' => [
                    'old_role' => UserRole::Customer->value,
                    'new_role' => UserRole::Provider->value,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        });

        return redirect()->route('provider.business.profile')
            ->with('success', 'Your provider account is ready. Set up your business profile to get started.');
    }
}
