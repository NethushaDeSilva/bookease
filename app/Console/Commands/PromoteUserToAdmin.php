<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdmin extends Command
{
    protected $signature = 'user:promote-admin {email : Email address of an existing user account}';

    protected $description = 'Promote an existing user to the admin role and ensure the account is active';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email [{$email}].");

            return self::FAILURE;
        }

        $user->role = UserRole::Admin;
        $user->status = UserStatus::Active;
        $user->save();

        $this->info("{$user->email} is now an active admin.");

        return self::SUCCESS;
    }
}
