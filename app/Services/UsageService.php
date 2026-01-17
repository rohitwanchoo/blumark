<?php

namespace App\Services;

use App\Models\User;
use App\Models\UsageRecord;
use App\Models\WatermarkJob;

class UsageService
{
    public function recordJobUsage(User $user, WatermarkJob $job, bool $useCredits = false): void
    {
        $source = $useCredits ? 'credits' : 'subscription';

        if ($useCredits) {
            $user->useCredits(1, "Watermark job #{$job->id}");
        }

        UsageRecord::create([
            'user_id' => $user->id,
            'watermark_job_id' => $job->id,
            'usage_date' => now()->toDateString(),
            'jobs_count' => 1,
            'pages_count' => $job->page_count ?? 0,
            'source' => $source,
        ]);
    }

    public function getMonthlyUsageStats(User $user): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $records = $user->usageRecords()
            ->whereBetween('usage_date', [$startOfMonth, $endOfMonth])
            ->get();

        return [
            'total_jobs' => $records->sum('jobs_count'),
            'total_pages' => $records->sum('pages_count'),
            'from_subscription' => $records->where('source', 'subscription')->sum('jobs_count'),
            'from_credits' => $records->where('source', 'credits')->sum('jobs_count'),
        ];
    }

    public function getDailyUsage(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return $user->usageRecords()
            ->where('usage_date', '>=', $startDate)
            ->selectRaw('usage_date, SUM(jobs_count) as jobs, SUM(pages_count) as pages')
            ->groupBy('usage_date')
            ->orderBy('usage_date')
            ->get()
            ->toArray();
    }
}
