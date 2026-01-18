<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\User;
use App\Models\WatermarkJob;
use App\Notifications\SuspiciousActivityNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AlertService
{
    /**
     * Create an alert and optionally notify admins.
     */
    public function createAlert(
        string $type,
        string $severity,
        string $title,
        string $description,
        array $metadata = [],
        ?string $ipAddress = null,
        ?WatermarkJob $job = null,
        bool $notifyAdmins = true
    ): ?Alert {
        // Deduplicate: Check if similar alert exists recently
        if ($this->isDuplicateAlert($type, $ipAddress, $job?->id)) {
            Log::debug("Duplicate alert suppressed", [
                'type' => $type,
                'ip' => $ipAddress,
                'job_id' => $job?->id,
            ]);
            return null;
        }

        $alert = Alert::create([
            'type' => $type,
            'severity' => $severity,
            'status' => Alert::STATUS_NEW,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'watermark_job_id' => $job?->id,
            'user_id' => $job?->user_id,
        ]);

        Log::warning("Security alert created", [
            'alert_id' => $alert->id,
            'type' => $type,
            'severity' => $severity,
            'ip' => $ipAddress,
        ]);

        if ($notifyAdmins && $this->shouldNotify($severity)) {
            $this->notifyAdmins($alert);
        }

        return $alert;
    }

    /**
     * Check if this is a duplicate alert within the deduplication window.
     */
    protected function isDuplicateAlert(string $type, ?string $ip, ?int $jobId): bool
    {
        $cacheKey = "alert_dedup:{$type}:{$ip}:{$jobId}";
        $window = config('watermark.alerts.deduplication_minutes', 30);

        if (Cache::has($cacheKey)) {
            return true;
        }

        Cache::put($cacheKey, true, now()->addMinutes($window));
        return false;
    }

    /**
     * Determine if admin notification should be sent for this severity.
     */
    protected function shouldNotify(string $severity): bool
    {
        $notifySeverities = config('watermark.alerts.notify_severities', ['critical', 'high']);
        return in_array($severity, $notifySeverities);
    }

    /**
     * Notify all admin users about the alert.
     */
    protected function notifyAdmins(Alert $alert): void
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new SuspiciousActivityNotification($alert));
            } catch (\Exception $e) {
                Log::error("Failed to notify admin about alert", [
                    'alert_id' => $alert->id,
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("Admin notifications sent for alert", [
            'alert_id' => $alert->id,
            'admin_count' => $admins->count(),
        ]);
    }

    /**
     * Create alert for multi-job IP access.
     */
    public function alertMultiJobIp(string $ip, int $jobCount, array $jobIds): ?Alert
    {
        return $this->createAlert(
            type: Alert::TYPE_MULTI_JOB_IP,
            severity: Alert::SEVERITY_MEDIUM,
            title: "Single IP accessing multiple documents",
            description: "IP address {$ip} has accessed {$jobCount} different documents in the last 24 hours. This may indicate unauthorized bulk access.",
            metadata: [
                'job_count' => $jobCount,
                'job_ids' => array_slice($jobIds, 0, 20), // Limit stored IDs
            ],
            ipAddress: $ip
        );
    }

    /**
     * Create alert for rapid successive downloads.
     */
    public function alertRapidDownload(string $ip, int $downloadCount, WatermarkJob $job): ?Alert
    {
        $threshold = config('watermark.alerts.rapid_download_threshold', 3);
        $window = config('watermark.alerts.rapid_download_window_minutes', 5);

        return $this->createAlert(
            type: Alert::TYPE_RAPID_DOWNLOAD,
            severity: Alert::SEVERITY_HIGH,
            title: "Rapid successive downloads detected",
            description: "IP address {$ip} downloaded document '{$job->original_filename}' {$downloadCount} times within {$window} minutes (threshold: {$threshold}).",
            metadata: [
                'download_count' => $downloadCount,
                'threshold' => $threshold,
                'window_minutes' => $window,
                'filename' => $job->original_filename,
            ],
            ipAddress: $ip,
            job: $job
        );
    }

    /**
     * Create alert for high-risk document.
     */
    public function alertHighRiskDocument(WatermarkJob $job, array $riskAssessment): ?Alert
    {
        return $this->createAlert(
            type: Alert::TYPE_HIGH_RISK_DOCUMENT,
            severity: Alert::SEVERITY_CRITICAL,
            title: "High-risk document activity detected",
            description: "Document '{$job->original_filename}' has been flagged as high-risk. Risk level: {$riskAssessment['level']}, Score: {$riskAssessment['score']}.",
            metadata: [
                'risk_score' => $riskAssessment['score'],
                'risk_level' => $riskAssessment['level'],
                'risk_factors' => $riskAssessment['factors'] ?? [],
                'recommendations' => $riskAssessment['recommendations'] ?? [],
                'filename' => $job->original_filename,
            ],
            job: $job
        );
    }

    /**
     * Create alert for excessive downloads on a document.
     */
    public function alertExcessiveDownloads(WatermarkJob $job, int $downloadCount, int $uniqueIps): ?Alert
    {
        return $this->createAlert(
            type: Alert::TYPE_EXCESSIVE_DOWNLOADS,
            severity: Alert::SEVERITY_HIGH,
            title: "Excessive downloads on document",
            description: "Document '{$job->original_filename}' has {$downloadCount} downloads from {$uniqueIps} unique IP addresses.",
            metadata: [
                'download_count' => $downloadCount,
                'unique_ips' => $uniqueIps,
                'filename' => $job->original_filename,
            ],
            job: $job
        );
    }

    /**
     * Create alert for unusual geographic access.
     */
    public function alertUnusualGeo(string $ip, string $country, WatermarkJob $job, int $accessCount): ?Alert
    {
        return $this->createAlert(
            type: Alert::TYPE_UNUSUAL_GEO,
            severity: Alert::SEVERITY_LOW,
            title: "Unusual geographic access pattern",
            description: "Document '{$job->original_filename}' received {$accessCount} accesses from {$country}.",
            metadata: [
                'country' => $country,
                'access_count' => $accessCount,
                'filename' => $job->original_filename,
            ],
            ipAddress: $ip,
            job: $job
        );
    }

    /**
     * Acknowledge an alert.
     */
    public function acknowledge(Alert $alert, User $admin): Alert
    {
        $alert->update([
            'status' => Alert::STATUS_ACKNOWLEDGED,
            'acknowledged_by' => $admin->id,
            'acknowledged_at' => now(),
        ]);

        Log::info("Alert acknowledged", [
            'alert_id' => $alert->id,
            'admin_id' => $admin->id,
        ]);

        return $alert->fresh();
    }

    /**
     * Resolve an alert.
     */
    public function resolve(Alert $alert, User $admin, ?string $notes = null): Alert
    {
        $alert->update([
            'status' => Alert::STATUS_RESOLVED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);

        Log::info("Alert resolved", [
            'alert_id' => $alert->id,
            'admin_id' => $admin->id,
        ]);

        return $alert->fresh();
    }

    /**
     * Dismiss an alert (false positive).
     */
    public function dismiss(Alert $alert, User $admin): Alert
    {
        $alert->update([
            'status' => Alert::STATUS_DISMISSED,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
            'resolution_notes' => 'Dismissed as false positive',
        ]);

        Log::info("Alert dismissed", [
            'alert_id' => $alert->id,
            'admin_id' => $admin->id,
        ]);

        return $alert->fresh();
    }

    /**
     * Get alert statistics.
     */
    public function getStats(): array
    {
        return [
            'new' => Alert::where('status', Alert::STATUS_NEW)->count(),
            'critical_new' => Alert::where('status', Alert::STATUS_NEW)
                ->where('severity', Alert::SEVERITY_CRITICAL)
                ->count(),
            'high_new' => Alert::where('status', Alert::STATUS_NEW)
                ->where('severity', Alert::SEVERITY_HIGH)
                ->count(),
            'today' => Alert::whereDate('created_at', today())->count(),
            'this_week' => Alert::where('created_at', '>=', now()->subWeek())->count(),
        ];
    }
}
