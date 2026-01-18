<?php

namespace App\Console\Commands;

use App\Models\AdminActivityLog;
use Illuminate\Console\Command;

class PurgeActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:purge-activity-logs
                            {--days=90 : Delete logs older than this many days}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old admin activity logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($days < 1) {
            $this->error('Days must be at least 1.');
            return self::FAILURE;
        }

        $cutoffDate = now()->subDays($days);
        $query = AdminActivityLog::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info("No activity logs older than {$days} days found.");
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("[Dry Run] Would delete {$count} activity log(s) older than {$days} days (before {$cutoffDate->format('Y-m-d')}).");

            // Show breakdown by action type
            $breakdown = AdminActivityLog::where('created_at', '<', $cutoffDate)
                ->selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action');

            if ($breakdown->isNotEmpty()) {
                $this->newLine();
                $this->info('Breakdown by action:');
                foreach ($breakdown as $action => $actionCount) {
                    $this->line("  - {$action}: {$actionCount}");
                }
            }

            return self::SUCCESS;
        }

        if (!$this->confirm("Are you sure you want to delete {$count} activity log(s) older than {$days} days?")) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} activity log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
