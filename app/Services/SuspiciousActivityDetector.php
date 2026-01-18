<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\DocumentAccessLog;
use App\Models\WatermarkJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuspiciousActivityDetector
{
    public function __construct(
        protected AlertService $alertService,
        protected AccessTrackingService $accessService
    ) {}

    /**
     * Analyze a new access log entry for suspicious patterns.
     * Called after each DocumentAccessLog is created.
     */
    public function analyzeAccess(DocumentAccessLog $log): void
    {
        // Only analyze downloads (most sensitive action)
        if ($log->action !== DocumentAccessLog::ACTION_DOWNLOAD) {
            return;
        }

        try {
            $this->checkRapidDownloads($log);
            $this->checkMultiJobAccess($log);
            $this->checkExcessiveDownloads($log);
        } catch (\Exception $e) {
            Log::error("Error in suspicious activity detection", [
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check for rapid successive downloads from same IP on same document.
     */
    protected function checkRapidDownloads(DocumentAccessLog $log): void
    {
        $threshold = config('watermark.alerts.rapid_download_threshold', 3);
        $windowMinutes = config('watermark.alerts.rapid_download_window_minutes', 5);

        $recentCount = DocumentAccessLog::where('ip_address', $log->ip_address)
            ->where('watermark_job_id', $log->watermark_job_id)
            ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->count();

        if ($recentCount >= $threshold) {
            $job = $log->watermarkJob;
            if ($job) {
                $this->alertService->alertRapidDownload(
                    $log->ip_address,
                    $recentCount,
                    $job
                );
            }
        }
    }

    /**
     * Check for single IP accessing multiple different documents.
     */
    protected function checkMultiJobAccess(DocumentAccessLog $log): void
    {
        $threshold = config('watermark.alerts.multi_job_threshold', 5);
        $hoursBack = config('watermark.alerts.multi_job_hours', 24);

        $jobCount = DocumentAccessLog::where('ip_address', $log->ip_address)
            ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
            ->where('created_at', '>=', now()->subHours($hoursBack))
            ->distinct('watermark_job_id')
            ->count('watermark_job_id');

        if ($jobCount >= $threshold) {
            $jobIds = DocumentAccessLog::where('ip_address', $log->ip_address)
                ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
                ->where('created_at', '>=', now()->subHours($hoursBack))
                ->distinct()
                ->pluck('watermark_job_id')
                ->toArray();

            $this->alertService->alertMultiJobIp(
                $log->ip_address,
                $jobCount,
                $jobIds
            );
        }
    }

    /**
     * Check for excessive total downloads on a document.
     */
    protected function checkExcessiveDownloads(DocumentAccessLog $log): void
    {
        $job = $log->watermarkJob;
        if (!$job) {
            return;
        }

        $threshold = config('watermark.alerts.excessive_download_threshold', 50);
        $stats = $this->accessService->getJobStatistics($job);

        if ($stats['total_downloads'] >= $threshold) {
            // Check if we've already alerted for this job recently
            $existingAlert = Alert::where('type', Alert::TYPE_EXCESSIVE_DOWNLOADS)
                ->where('watermark_job_id', $job->id)
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if (!$existingAlert) {
                $this->alertService->alertExcessiveDownloads(
                    $job,
                    $stats['total_downloads'],
                    $stats['unique_ips']
                );
            }
        }
    }

    /**
     * Run a full scan for suspicious patterns (for scheduled tasks).
     * This is useful for catching patterns that span multiple requests.
     */
    public function runFullScan(int $hoursBack = 24): array
    {
        $patterns = $this->accessService->getSuspiciousPatterns(null, $hoursBack);
        $alertsCreated = [];

        // Process multi-job IP patterns
        foreach ($patterns['multi_job_ips'] as $pattern) {
            $alert = $this->alertService->alertMultiJobIp(
                $pattern->ip_address,
                $pattern->job_count,
                []
            );
            if ($alert) {
                $alertsCreated[] = $alert->id;
            }
        }

        // Process rapid download patterns
        foreach ($patterns['rapid_downloads'] as $pattern) {
            $job = WatermarkJob::find($pattern->watermark_job_id);
            if ($job) {
                $alert = $this->alertService->alertRapidDownload(
                    $pattern->ip_address,
                    $pattern->download_count,
                    $job
                );
                if ($alert) {
                    $alertsCreated[] = $alert->id;
                }
            }
        }

        // Process geographic anomalies
        foreach ($patterns['geo_patterns'] as $pattern) {
            $job = WatermarkJob::find($pattern->watermark_job_id);
            if ($job && $pattern->geo_country) {
                $alert = $this->alertService->alertUnusualGeo(
                    '', // No specific IP for geo patterns
                    $pattern->geo_country,
                    $job,
                    $pattern->count
                );
                if ($alert) {
                    $alertsCreated[] = $alert->id;
                }
            }
        }

        Log::info("Suspicious activity scan completed", [
            'hours_back' => $hoursBack,
            'alerts_created' => count($alertsCreated),
        ]);

        return $alertsCreated;
    }

    /**
     * Analyze a specific job for risk indicators.
     */
    public function analyzeJobRisk(WatermarkJob $job): array
    {
        $stats = $this->accessService->getJobStatistics($job);
        $fingerprints = $job->fingerprints;
        $recipientCount = $fingerprints->count();

        $riskScore = 0;
        $factors = [];

        // Factor 1: High distribution (>10 recipients)
        if ($recipientCount > 10) {
            $riskScore += 20;
            $factors[] = "High distribution ({$recipientCount} recipients)";
        }

        // Factor 2: Downloads significantly exceed recipient count
        if ($recipientCount > 0 && $stats['total_downloads'] > $recipientCount * 3) {
            $riskScore += 25;
            $factors[] = "Downloads ({$stats['total_downloads']}) exceed expected ({$recipientCount})";
        }

        // Factor 3: Many unique IPs
        if ($stats['unique_ips'] > $recipientCount * 2) {
            $riskScore += 20;
            $factors[] = "Unusual IP diversity ({$stats['unique_ips']} unique IPs)";
        }

        // Factor 4: Recent activity spike
        $recentDownloads = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($recentDownloads > 10) {
            $riskScore += 15;
            $factors[] = "Recent activity spike ({$recentDownloads} downloads in last hour)";
        }

        // Determine risk level
        $level = 'low';
        if ($riskScore >= 40) {
            $level = 'high';
        } elseif ($riskScore >= 20) {
            $level = 'medium';
        }

        return [
            'score' => $riskScore,
            'level' => $level,
            'factors' => $factors,
            'stats' => $stats,
            'recommendations' => $this->getRecommendations($level, $factors),
        ];
    }

    /**
     * Get recommendations based on risk assessment.
     */
    protected function getRecommendations(string $level, array $factors): array
    {
        $recommendations = [];

        if ($level === 'high') {
            $recommendations[] = 'Review document distribution immediately';
            $recommendations[] = 'Consider revoking active shared links';
            $recommendations[] = 'Contact document owner';
        } elseif ($level === 'medium') {
            $recommendations[] = 'Monitor document access over next 24 hours';
            $recommendations[] = 'Review recipient list for unauthorized entries';
        }

        if (in_array('Unusual IP diversity', array_map(fn($f) => explode(' (', $f)[0], $factors))) {
            $recommendations[] = 'Investigate IP addresses for potential credential sharing';
        }

        return $recommendations;
    }
}
