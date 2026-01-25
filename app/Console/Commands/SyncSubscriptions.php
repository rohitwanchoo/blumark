<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Cashier\Subscription;

class SyncSubscriptions extends Command
{
    protected $signature = 'subscriptions:sync {--user= : Sync a specific user by ID or email}';

    protected $description = 'Sync user subscriptions with Stripe';

    public function handle(): int
    {
        $this->info('Starting subscription sync with Stripe...');

        $query = Subscription::query();

        if ($userId = $this->option('user')) {
            $user = is_numeric($userId)
                ? User::find($userId)
                : User::where('email', $userId)->first();

            if (!$user) {
                $this->error("User not found: {$userId}");
                return Command::FAILURE;
            }

            $query->where('user_id', $user->id);
            $this->info("Syncing subscriptions for user: {$user->email}");
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions found to sync.');
            return Command::SUCCESS;
        }

        $this->info("Found {$subscriptions->count()} subscription(s) to sync.");

        $synced = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($subscriptions->count());
        $bar->start();

        foreach ($subscriptions as $subscription) {
            try {
                $oldStatus = $subscription->stripe_status;
                $subscription->syncStripeStatus();
                $subscription->refresh();

                if ($oldStatus !== $subscription->stripe_status) {
                    $this->newLine();
                    $this->line("  User #{$subscription->user_id}: {$oldStatus} -> {$subscription->stripe_status}");
                }

                $synced++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("  Error syncing subscription #{$subscription->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sync complete: {$synced} synced, {$errors} errors.");

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
