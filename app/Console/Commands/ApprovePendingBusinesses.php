<?php

namespace App\Console\Commands;

use App\Enums\BusinessStatus;
use App\Models\Business;
use Illuminate\Console\Command;

class ApprovePendingBusinesses extends Command
{
    protected $signature = 'business:approve-pending';

    protected $description = 'List every business and approve any that are still pending';

    public function handle(): int
    {
        $businesses = Business::all();

        if ($businesses->isEmpty()) {
            $this->info('No businesses found.');

            return self::SUCCESS;
        }

        foreach ($businesses as $business) {
            $this->line("{$business->id} | {$business->name} | {$business->status->value}");
        }

        $pending = $businesses->where('status', BusinessStatus::Pending);

        if ($pending->isEmpty()) {
            $this->info('No pending businesses to approve.');

            return self::SUCCESS;
        }

        foreach ($pending as $business) {
            $business->status = BusinessStatus::Active;
            $business->save();

            $this->info("Approved: {$business->name}");
        }

        return self::SUCCESS;
    }
}
