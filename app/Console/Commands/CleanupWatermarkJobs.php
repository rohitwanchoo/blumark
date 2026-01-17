<?php

namespace App\Console\Commands;

use App\Models\WatermarkJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupWatermarkJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watermark:cleanup
                            {--days= : Number of days to retain files (default from config)}
                            {--dry-run : Run without actually deleting files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old watermark jobs and their associated files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionDays = $this->option('days') ?? config('watermark.retention_days', 7);
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up watermark jobs older than {$retentionDays} days...");

        if ($dryRun) {
            $this->warn('Running in dry-run mode. No files will be deleted.');
        }

        $jobs = WatermarkJob::olderThan($retentionDays)->get();

        if ($jobs->isEmpty()) {
            $this->info('No jobs to clean up.');
            return Command::SUCCESS;
        }

        $this->info("Found {$jobs->count()} jobs to clean up.");

        $deletedCount = 0;
        $errorCount = 0;

        $bar = $this->output->createProgressBar($jobs->count());
        $bar->start();

        foreach ($jobs as $job) {
            try {
                if (!$dryRun) {
                    // Delete associated files
                    $job->deleteFiles();

                    // Delete the database record
                    $job->delete();
                }

                $deletedCount++;

                Log::info('Cleaned up watermark job', [
                    'job_id' => $job->id,
                    'user_id' => $job->user_id,
                    'created_at' => $job->created_at->toIso8601String(),
                    'dry_run' => $dryRun,
                ]);

            } catch (\Exception $e) {
                $errorCount++;

                Log::error('Failed to clean up watermark job', [
                    'job_id' => $job->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Failed to clean up job {$job->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Cleanup complete. Deleted: {$deletedCount}, Errors: {$errorCount}");

        if ($dryRun) {
            $this->warn("This was a dry run. Run without --dry-run to actually delete files.");
        }

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
