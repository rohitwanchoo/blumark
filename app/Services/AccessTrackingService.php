<?php

namespace App\Services;

use App\Models\DocumentAccessLog;
use App\Models\DocumentFingerprint;
use App\Models\WatermarkJob;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccessTrackingService
{
    /**
     * Log a download event.
     */
    public function logDownload(
        WatermarkJob $job,
        Request $request,
        ?string $recipientEmail = null,
        ?DocumentFingerprint $fingerprint = null
    ): DocumentAccessLog {
        return $this->logAccess(
            job: $job,
            action: DocumentAccessLog::ACTION_DOWNLOAD,
            request: $request,
            recipientEmail: $recipientEmail,
            fingerprint: $fingerprint
        );
    }

    /**
     * Log a view event.
     */
    public function logView(
        WatermarkJob $job,
        Request $request,
        ?string $recipientEmail = null,
        ?DocumentFingerprint $fingerprint = null
    ): DocumentAccessLog {
        return $this->logAccess(
            job: $job,
            action: DocumentAccessLog::ACTION_VIEW,
            request: $request,
            recipientEmail: $recipientEmail,
            fingerprint: $fingerprint
        );
    }

    /**
     * Log a verification event.
     */
    public function logVerification(
        DocumentFingerprint $fingerprint,
        Request $request
    ): DocumentAccessLog {
        return $this->logAccess(
            job: $fingerprint->watermarkJob,
            action: DocumentAccessLog::ACTION_VERIFY,
            request: $request,
            fingerprint: $fingerprint
        );
    }

    /**
     * Log a share event.
     */
    public function logShare(
        WatermarkJob $job,
        Request $request,
        ?string $recipientEmail = null
    ): DocumentAccessLog {
        return $this->logAccess(
            job: $job,
            action: DocumentAccessLog::ACTION_SHARE,
            request: $request,
            recipientEmail: $recipientEmail
        );
    }

    /**
     * Log a shared link access event.
     */
    public function logSharedLinkAccess(
        WatermarkJob $job,
        Request $request,
        string $shareToken,
        ?string $recipientEmail = null
    ): DocumentAccessLog {
        $geo = $this->getGeoLocation($request->ip());

        return DocumentAccessLog::create([
            'watermark_job_id' => $job->id,
            'fingerprint_id' => null,
            'action' => DocumentAccessLog::ACTION_DOWNLOAD,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'recipient_email' => $recipientEmail,
            'geo_country' => $geo['country'] ?? null,
            'geo_city' => $geo['city'] ?? null,
            'metadata' => [
                'share_token' => $shareToken,
                'access_type' => 'shared_link',
            ],
        ]);
    }

    /**
     * Core logging method.
     */
    protected function logAccess(
        ?WatermarkJob $job,
        string $action,
        Request $request,
        ?string $recipientEmail = null,
        ?DocumentFingerprint $fingerprint = null
    ): DocumentAccessLog {
        $geo = $this->getGeoLocation($request->ip());

        return DocumentAccessLog::create([
            'watermark_job_id' => $job?->id,
            'fingerprint_id' => $fingerprint?->id,
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'recipient_email' => $recipientEmail ?? $fingerprint?->recipient_email,
            'geo_country' => $geo['country'] ?? null,
            'geo_city' => $geo['city'] ?? null,
        ]);
    }

    /**
     * Get geo location from IP address.
     */
    protected function getGeoLocation(?string $ip): array
    {
        if (!$ip || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['country' => null, 'city' => 'Local'];
        }

        // In production, you would use a geo-IP service
        // For now, return empty or use a simple lookup
        try {
            // Example with ip-api.com (free tier, limited)
            // $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
            // if ($response->successful()) {
            //     $data = $response->json();
            //     return ['country' => $data['countryCode'] ?? null, 'city' => $data['city'] ?? null];
            // }
            return ['country' => null, 'city' => null];
        } catch (\Exception $e) {
            return ['country' => null, 'city' => null];
        }
    }

    /**
     * Get access history for a job.
     */
    public function getAccessHistory(WatermarkJob $job, ?int $limit = 50): Collection
    {
        return DocumentAccessLog::where('watermark_job_id', $job->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get access history for a fingerprint.
     */
    public function getFingerprintHistory(DocumentFingerprint $fingerprint, ?int $limit = 50): Collection
    {
        return DocumentAccessLog::where('fingerprint_id', $fingerprint->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all access logs for a user's jobs.
     */
    public function getUserAccessLogs(int $userId, ?int $limit = 100): Collection
    {
        return DocumentAccessLog::whereHas('watermarkJob', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['watermarkJob', 'fingerprint'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get access statistics for a job.
     */
    public function getJobStatistics(WatermarkJob $job): array
    {
        $stats = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $uniqueIps = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->distinct('ip_address')
            ->count('ip_address');

        $uniqueRecipients = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->whereNotNull('recipient_email')
            ->distinct('recipient_email')
            ->count('recipient_email');

        $firstAccess = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->orderBy('created_at', 'asc')
            ->first();

        $lastAccess = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'total_downloads' => $stats[DocumentAccessLog::ACTION_DOWNLOAD] ?? 0,
            'total_views' => $stats[DocumentAccessLog::ACTION_VIEW] ?? 0,
            'total_verifications' => $stats[DocumentAccessLog::ACTION_VERIFY] ?? 0,
            'total_shares' => $stats[DocumentAccessLog::ACTION_SHARE] ?? 0,
            'unique_ips' => $uniqueIps,
            'unique_recipients' => $uniqueRecipients,
            'first_access' => $firstAccess?->created_at,
            'last_access' => $lastAccess?->created_at,
        ];
    }

    /**
     * Get suspicious access patterns.
     */
    public function getSuspiciousPatterns(?int $userId = null, int $hoursBack = 24): array
    {
        $since = now()->subHours($hoursBack);

        $query = DocumentAccessLog::where('created_at', '>=', $since);

        if ($userId) {
            $query->whereHas('watermarkJob', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Pattern 1: Multiple downloads from same IP to different jobs
        $multiJobIps = $query->clone()
            ->select('ip_address', DB::raw('count(distinct watermark_job_id) as job_count'))
            ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
            ->groupBy('ip_address')
            ->having('job_count', '>', 5)
            ->get();

        // Pattern 2: Rapid successive downloads
        $rapidDownloads = $this->detectRapidDownloads($query->clone(), $since);

        // Pattern 3: Unusual geographic patterns
        $geoPatterns = $query->clone()
            ->select('watermark_job_id', 'geo_country', DB::raw('count(*) as count'))
            ->whereNotNull('geo_country')
            ->groupBy('watermark_job_id', 'geo_country')
            ->having('count', '>', 10)
            ->get();

        // Pattern 4: Access from known VPN/proxy ranges (would need a database)
        // This is a placeholder for that functionality

        return [
            'multi_job_ips' => $multiJobIps,
            'rapid_downloads' => $rapidDownloads,
            'geo_patterns' => $geoPatterns,
            'analysis_period_hours' => $hoursBack,
        ];
    }

    /**
     * Detect rapid successive downloads.
     */
    protected function detectRapidDownloads($query, $since): Collection
    {
        // Get downloads grouped by IP and time window
        return $query
            ->where('action', DocumentAccessLog::ACTION_DOWNLOAD)
            ->select(
                'ip_address',
                'watermark_job_id',
                DB::raw('count(*) as download_count'),
                DB::raw('min(created_at) as first_download'),
                DB::raw('max(created_at) as last_download')
            )
            ->groupBy('ip_address', 'watermark_job_id')
            ->having('download_count', '>', 3)
            ->get()
            ->filter(function ($item) {
                // Flag if more than 3 downloads within 5 minutes
                $duration = strtotime($item->last_download) - strtotime($item->first_download);
                return $duration < 300; // 5 minutes
            });
    }

    /**
     * Get access timeline for a job.
     */
    public function getAccessTimeline(WatermarkJob $job, int $days = 30): array
    {
        $since = now()->subDays($days);

        $timeline = DocumentAccessLog::where('watermark_job_id', $job->id)
            ->where('created_at', '>=', $since)
            ->select(
                DB::raw('DATE(created_at) as date'),
                'action',
                DB::raw('count(*) as count')
            )
            ->groupBy('date', 'action')
            ->orderBy('date')
            ->get();

        // Organize by date
        $organized = [];
        foreach ($timeline as $entry) {
            $date = $entry->date;
            if (!isset($organized[$date])) {
                $organized[$date] = [
                    'date' => $date,
                    'downloads' => 0,
                    'views' => 0,
                    'verifications' => 0,
                    'shares' => 0,
                ];
            }
            $organized[$date][$entry->action . 's'] = $entry->count;
        }

        return array_values($organized);
    }

    /**
     * Export access logs for a job.
     */
    public function exportAccessLogs(WatermarkJob $job, string $format = 'csv'): string
    {
        $logs = $this->getAccessHistory($job, 10000);

        if ($format === 'csv') {
            return $this->generateCsv($logs);
        }

        return $this->generateJson($logs);
    }

    /**
     * Generate CSV export.
     */
    protected function generateCsv(Collection $logs): string
    {
        $output = "Date,Time,Action,IP Address,Recipient,User Agent,Geo Location\n";

        foreach ($logs as $log) {
            $output .= sprintf(
                "%s,%s,%s,%s,%s,\"%s\",%s\n",
                $log->created_at->format('Y-m-d'),
                $log->created_at->format('H:i:s'),
                $log->action,
                $log->ip_address ?? '',
                $log->recipient_email ?? '',
                str_replace('"', '""', $log->user_agent ?? ''),
                trim(($log->geo_city ?? '') . ', ' . ($log->geo_country ?? ''), ', ')
            );
        }

        return $output;
    }

    /**
     * Generate JSON export.
     */
    protected function generateJson(Collection $logs): string
    {
        return $logs->map(function ($log) {
            return [
                'date' => $log->created_at->toIso8601String(),
                'action' => $log->action,
                'ip_address' => $log->ip_address,
                'recipient' => $log->recipient_email,
                'user_agent' => $log->user_agent,
                'geo_country' => $log->geo_country,
                'geo_city' => $log->geo_city,
            ];
        })->toJson(JSON_PRETTY_PRINT);
    }

    /**
     * Clean up old access logs.
     */
    public function cleanupOldLogs(int $daysToKeep = 365): int
    {
        $cutoff = now()->subDays($daysToKeep);

        return DocumentAccessLog::where('created_at', '<', $cutoff)->delete();
    }
}
