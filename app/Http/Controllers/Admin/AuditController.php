<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentAccessLog;
use App\Models\DocumentFingerprint;
use App\Models\VerificationAttempt;
use App\Models\WatermarkJob;
use App\Services\AccessTrackingService;
use App\Services\LeakDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function __construct(
        protected AccessTrackingService $accessService,
        protected LeakDetectionService $leakService
    ) {}

    /**
     * Overview of all document access.
     */
    public function index(Request $request)
    {
        // Summary statistics
        $stats = [
            'total_downloads' => DocumentAccessLog::where('action', 'download')->count(),
            'total_verifications' => VerificationAttempt::count(),
            'total_fingerprints' => DocumentFingerprint::count(),
            'unique_recipients' => DocumentFingerprint::distinct('recipient_email')->count('recipient_email'),
        ];

        // Recent access logs
        $recentLogs = DocumentAccessLog::with(['watermarkJob', 'fingerprint'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Access by action type (last 30 days)
        $accessByAction = DocumentAccessLog::where('created_at', '>=', now()->subDays(30))
            ->select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        // Top accessed documents
        $topDocuments = DocumentAccessLog::select('watermark_job_id', DB::raw('count(*) as access_count'))
            ->groupBy('watermark_job_id')
            ->orderBy('access_count', 'desc')
            ->limit(10)
            ->with('watermarkJob')
            ->get();

        // Verification attempts summary
        $verificationStats = VerificationAttempt::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.audit.index', compact(
            'stats',
            'recentLogs',
            'accessByAction',
            'topDocuments',
            'verificationStats'
        ));
    }

    /**
     * Access history for a specific job.
     */
    public function job(WatermarkJob $job)
    {
        $accessLogs = $this->accessService->getAccessHistory($job, 100);
        $stats = $this->accessService->getJobStatistics($job);
        $timeline = $this->accessService->getAccessTimeline($job, 30);
        $fingerprints = $job->fingerprints()->get();

        // Suspicious patterns for this job
        $suspiciousPatterns = $this->accessService->getSuspiciousPatterns($job->user_id, 72);

        return view('admin.audit.job', compact(
            'job',
            'accessLogs',
            'stats',
            'timeline',
            'fingerprints',
            'suspiciousPatterns'
        ));
    }

    /**
     * View potential leaks.
     */
    public function leaks(Request $request)
    {
        $daysBack = $request->input('days', 30);

        // Get potential leaks
        $potentialLeaks = $this->leakService->getPotentialLeaks(null, $daysBack);

        // Get recent leak reports (if you store them)
        // For now, we'll show suspicious access patterns
        $suspiciousPatterns = $this->accessService->getSuspiciousPatterns(null, 48);

        // Jobs with high risk scores
        $highRiskJobs = WatermarkJob::whereHas('accessLogs')
            ->withCount('accessLogs')
            ->having('access_logs_count', '>', 20)
            ->orderBy('access_logs_count', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($job) {
                $fingerprints = $job->fingerprints;
                $accessLogs = $job->accessLogs;
                return [
                    'job' => $job,
                    'risk' => $this->calculateRiskScore($job, $accessLogs, $fingerprints),
                ];
            })
            ->filter(fn($item) => $item['risk']['level'] !== 'low')
            ->sortByDesc(fn($item) => $item['risk']['score']);

        return view('admin.audit.leaks', compact(
            'potentialLeaks',
            'suspiciousPatterns',
            'highRiskJobs',
            'daysBack'
        ));
    }

    /**
     * Calculate risk score for a job.
     */
    protected function calculateRiskScore($job, $accessLogs, $fingerprints): array
    {
        $riskScore = 0;
        $riskFactors = [];

        $recipientCount = $fingerprints->count();
        $uniqueIps = $accessLogs->pluck('ip_address')->unique()->count();
        $downloads = $accessLogs->where('action', 'download')->count();

        if ($uniqueIps > $recipientCount * 2) {
            $riskScore += 30;
            $riskFactors[] = "Many unique IPs ({$uniqueIps})";
        }

        if ($downloads > $recipientCount * 3) {
            $riskScore += 20;
            $riskFactors[] = "High download count ({$downloads})";
        }

        if ($recipientCount > 10) {
            $riskScore += 10;
            $riskFactors[] = "Wide distribution";
        }

        return [
            'score' => $riskScore,
            'level' => $riskScore >= 40 ? 'high' : ($riskScore >= 20 ? 'medium' : 'low'),
            'factors' => $riskFactors,
        ];
    }

    /**
     * Investigate a specific document.
     */
    public function investigate(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:50000',
        ]);

        $file = $request->file('document');
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            $investigation = $this->leakService->investigate($fullPath);

            return view('admin.audit.investigation', [
                'investigation' => $investigation,
                'filename' => $file->getClientOriginalName(),
            ]);
        } finally {
            @unlink($fullPath);
        }
    }

    /**
     * Export access logs.
     */
    public function export(Request $request, WatermarkJob $job)
    {
        $format = $request->input('format', 'csv');
        $content = $this->accessService->exportAccessLogs($job, $format);

        $filename = "access_logs_{$job->id}_" . now()->format('Y-m-d') . '.' . $format;

        return response($content)
            ->header('Content-Type', $format === 'csv' ? 'text/csv' : 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * View verification attempts.
     */
    public function verifications(Request $request)
    {
        $query = VerificationAttempt::with('fingerprint.watermarkJob')
            ->orderBy('created_at', 'desc');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $verifications = $query->paginate(50);

        $statusCounts = VerificationAttempt::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.audit.verifications', compact('verifications', 'statusCounts'));
    }

    /**
     * View fingerprint details.
     */
    public function fingerprint(DocumentFingerprint $fingerprint)
    {
        $fingerprint->load(['watermarkJob', 'accessLogs', 'verificationAttempts']);

        $accessLogs = $fingerprint->accessLogs()->orderBy('created_at', 'desc')->limit(50)->get();
        $verifications = $fingerprint->verificationAttempts()->orderBy('created_at', 'desc')->limit(20)->get();

        return view('admin.audit.fingerprint', compact('fingerprint', 'accessLogs', 'verifications'));
    }
}
