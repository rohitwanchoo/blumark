<?php

namespace App\Services;

use App\Models\DocumentAccessLog;
use App\Models\DocumentFingerprint;
use App\Models\WatermarkJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeakDetectionService
{
    protected InvisibleWatermarkService $invisibleService;
    protected DocumentFingerprintService $fingerprintService;
    protected AccessTrackingService $accessService;

    public function __construct(
        InvisibleWatermarkService $invisibleService,
        DocumentFingerprintService $fingerprintService,
        AccessTrackingService $accessService
    ) {
        $this->invisibleService = $invisibleService;
        $this->fingerprintService = $fingerprintService;
        $this->accessService = $accessService;
    }

    /**
     * Analyze an uploaded document to trace its origin.
     */
    public function analyzeDocument(string $pdfPath): array
    {
        $result = [
            'traced' => false,
            'fingerprint' => null,
            'job' => null,
            'recipient' => null,
            'markers_found' => [],
            'analysis' => [],
        ];

        // Try to extract invisible watermark
        $invisibleData = $this->invisibleService->extractInvisible($pdfPath);
        if ($invisibleData && isset($invisibleData['marker'])) {
            $result['markers_found'][] = [
                'type' => 'invisible',
                'source' => $invisibleData['source'] ?? 'unknown',
                'marker' => $invisibleData['marker'],
            ];
        }

        // Try to extract from document structure
        $structureMarker = $this->fingerprintService->extractUniqueMarker($pdfPath);
        if ($structureMarker) {
            $result['markers_found'][] = [
                'type' => 'structure',
                'marker' => $structureMarker,
            ];
        }

        // Try to find fingerprint by marker
        $marker = $invisibleData['marker'] ?? $structureMarker ?? null;
        if ($marker) {
            $fingerprint = $this->fingerprintService->findByMarker($marker);
            if ($fingerprint) {
                $result['traced'] = true;
                $result['fingerprint'] = $fingerprint;
                $result['job'] = $fingerprint->watermarkJob;
                $result['recipient'] = [
                    'email' => $fingerprint->recipient_email,
                    'name' => $fingerprint->recipient_name,
                    'issued_at' => $fingerprint->created_at,
                ];
            }
        }

        // Verify document hasn't been modified
        if ($result['fingerprint']) {
            $verification = $this->fingerprintService->verifyUploadedDocument($pdfPath);
            $result['analysis']['verification'] = $verification;
            $result['analysis']['modified'] = $verification['status'] === 'modified';
        }

        return $result;
    }

    /**
     * Trace a document back to its original recipient.
     */
    public function traceRecipient(string $uniqueMarker): ?array
    {
        $fingerprint = DocumentFingerprint::where('unique_marker', $uniqueMarker)->first();

        if (!$fingerprint) {
            return null;
        }

        return [
            'fingerprint' => $fingerprint,
            'recipient' => [
                'email' => $fingerprint->recipient_email,
                'name' => $fingerprint->recipient_name,
                'id' => $fingerprint->recipient_id,
            ],
            'document' => [
                'job_id' => $fingerprint->watermark_job_id,
                'issued_at' => $fingerprint->created_at,
                'verified_at' => $fingerprint->verified_at,
            ],
            'job' => $fingerprint->watermarkJob,
        ];
    }

    /**
     * Report a leak incident.
     */
    public function reportLeak(
        WatermarkJob $job,
        string $foundLocation,
        ?string $reporterEmail = null,
        ?array $additionalInfo = []
    ): array {
        $report = [
            'id' => uniqid('LEAK-'),
            'job_id' => $job->id,
            'reported_at' => now()->toIso8601String(),
            'found_location' => $foundLocation,
            'reporter_email' => $reporterEmail,
            'status' => 'reported',
            'investigation' => [],
        ];

        // Gather job information
        $report['job_info'] = [
            'original_filename' => $job->original_filename,
            'created_at' => $job->created_at,
            'owner_id' => $job->user_id,
        ];

        // Get all fingerprints for this job
        $fingerprints = $job->fingerprints()->get();
        $report['fingerprints_count'] = $fingerprints->count();
        $report['recipients'] = $fingerprints->map(function ($fp) {
            return [
                'email' => $fp->recipient_email,
                'name' => $fp->recipient_name,
                'marker' => $fp->unique_marker,
                'issued_at' => $fp->created_at,
            ];
        })->toArray();

        // Get access history
        $accessLogs = $this->accessService->getAccessHistory($job, 100);
        $report['access_summary'] = [
            'total_accesses' => $accessLogs->count(),
            'unique_ips' => $accessLogs->pluck('ip_address')->unique()->count(),
            'last_access' => $accessLogs->first()?->created_at,
        ];

        // Log the leak report
        Log::warning('Leak report filed', [
            'report_id' => $report['id'],
            'job_id' => $job->id,
            'location' => $foundLocation,
        ]);

        return $report;
    }

    /**
     * Investigate a potential leak.
     */
    public function investigate(string $pdfPath, ?WatermarkJob $suspectedJob = null): array
    {
        $investigation = [
            'started_at' => now()->toIso8601String(),
            'document_analysis' => null,
            'traced_recipient' => null,
            'access_pattern' => null,
            'suspects' => [],
            'conclusion' => null,
        ];

        // Analyze the document
        $analysis = $this->analyzeDocument($pdfPath);
        $investigation['document_analysis'] = $analysis;

        if ($analysis['traced']) {
            $fingerprint = $analysis['fingerprint'];
            $job = $analysis['job'];

            // Get recipient info
            $investigation['traced_recipient'] = [
                'email' => $fingerprint->recipient_email,
                'name' => $fingerprint->recipient_name,
                'marker' => $fingerprint->unique_marker,
                'issued_at' => $fingerprint->created_at,
            ];

            // Analyze access patterns for this fingerprint
            $accessLogs = DocumentAccessLog::where('fingerprint_id', $fingerprint->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $investigation['access_pattern'] = [
                'total_accesses' => $accessLogs->count(),
                'downloads' => $accessLogs->where('action', 'download')->count(),
                'unique_ips' => $accessLogs->pluck('ip_address')->unique()->values()->toArray(),
                'last_access' => $accessLogs->first()?->created_at,
                'timeline' => $accessLogs->take(10)->map(function ($log) {
                    return [
                        'action' => $log->action,
                        'ip' => $log->ip_address,
                        'date' => $log->created_at,
                        'location' => trim(($log->geo_city ?? '') . ', ' . ($log->geo_country ?? ''), ', ') ?: null,
                    ];
                })->toArray(),
            ];

            // Determine conclusion
            $investigation['conclusion'] = $this->determineConclusion($investigation);
        } else {
            $investigation['conclusion'] = [
                'certainty' => 'low',
                'message' => 'Could not trace document to a specific recipient. Document may have been sanitized or is not from this system.',
            ];
        }

        return $investigation;
    }

    /**
     * Determine investigation conclusion.
     */
    protected function determineConclusion(array $investigation): array
    {
        $recipient = $investigation['traced_recipient'] ?? null;
        $accessPattern = $investigation['access_pattern'] ?? null;

        if (!$recipient) {
            return [
                'certainty' => 'low',
                'message' => 'Unable to trace document origin.',
                'suspected_source' => null,
            ];
        }

        $certainty = 'high';
        $factors = [];

        // Check if document was modified
        if ($investigation['document_analysis']['analysis']['modified'] ?? false) {
            $certainty = 'medium';
            $factors[] = 'Document shows signs of modification after original distribution';
        }

        // Check access patterns
        if ($accessPattern) {
            if ($accessPattern['downloads'] > 5) {
                $factors[] = 'Document was downloaded multiple times';
            }
            if (count($accessPattern['unique_ips']) > 3) {
                $certainty = 'medium';
                $factors[] = 'Document was accessed from multiple IP addresses';
            }
        }

        return [
            'certainty' => $certainty,
            'message' => $certainty === 'high'
                ? "Document traced to recipient: {$recipient['email']}"
                : "Document likely originated from: {$recipient['email']} (with caveats)",
            'suspected_source' => $recipient['email'],
            'factors' => $factors,
            'recommendation' => $this->getRecommendation($certainty, $factors),
        ];
    }

    /**
     * Get recommendation based on investigation.
     */
    protected function getRecommendation(string $certainty, array $factors): string
    {
        if ($certainty === 'high') {
            return 'Contact the identified recipient to investigate the leak. Review access logs for unauthorized sharing.';
        }

        if ($certainty === 'medium') {
            return 'The source is likely identified but there is some uncertainty. Consider interviewing the recipient and reviewing their access history.';
        }

        return 'Unable to determine source with confidence. Consider implementing stronger tracking measures for future documents.';
    }

    /**
     * Get potential leak candidates based on suspicious patterns.
     */
    public function getPotentialLeaks(?int $userId = null, int $daysBack = 30): Collection
    {
        $since = now()->subDays($daysBack);

        // Find jobs with unusual access patterns
        $query = DocumentAccessLog::where('created_at', '>=', $since)
            ->select(
                'watermark_job_id',
                DB::raw('count(*) as access_count'),
                DB::raw('count(distinct ip_address) as unique_ips'),
                DB::raw('count(distinct fingerprint_id) as unique_fingerprints')
            )
            ->groupBy('watermark_job_id')
            ->having('unique_ips', '>', 10) // More than 10 unique IPs is suspicious
            ->orHaving('access_count', '>', 50); // More than 50 accesses is suspicious

        if ($userId) {
            $query->whereHas('watermarkJob', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        return $query->with('watermarkJob')->get();
    }

    /**
     * Generate a leak analysis report.
     */
    public function generateReport(WatermarkJob $job): array
    {
        $fingerprints = $job->fingerprints()->get();
        $accessLogs = $this->accessService->getAccessHistory($job, 1000);
        $stats = $this->accessService->getJobStatistics($job);

        $report = [
            'generated_at' => now()->toIso8601String(),
            'job' => [
                'id' => $job->id,
                'filename' => $job->original_filename,
                'created_at' => $job->created_at,
            ],
            'distribution' => [
                'total_fingerprints' => $fingerprints->count(),
                'recipients' => $fingerprints->map(fn($fp) => [
                    'email' => $fp->recipient_email,
                    'marker' => $fp->unique_marker,
                    'issued' => $fp->created_at,
                ])->toArray(),
            ],
            'access_statistics' => $stats,
            'risk_assessment' => $this->assessLeakRisk($job, $accessLogs, $fingerprints),
        ];

        return $report;
    }

    /**
     * Assess leak risk for a job.
     */
    protected function assessLeakRisk(WatermarkJob $job, Collection $accessLogs, Collection $fingerprints): array
    {
        $riskScore = 0;
        $riskFactors = [];

        // Factor 1: Number of recipients
        $recipientCount = $fingerprints->count();
        if ($recipientCount > 10) {
            $riskScore += 20;
            $riskFactors[] = "High distribution ({$recipientCount} recipients)";
        } elseif ($recipientCount > 5) {
            $riskScore += 10;
            $riskFactors[] = "Moderate distribution ({$recipientCount} recipients)";
        }

        // Factor 2: Access from unknown IPs
        $uniqueIps = $accessLogs->pluck('ip_address')->unique()->count();
        $expectedIps = $recipientCount * 2; // Allow 2 IPs per recipient
        if ($uniqueIps > $expectedIps) {
            $excess = $uniqueIps - $expectedIps;
            $riskScore += min(30, $excess * 5);
            $riskFactors[] = "Access from {$excess} unexpected IP addresses";
        }

        // Factor 3: Download frequency
        $downloads = $accessLogs->where('action', 'download')->count();
        if ($downloads > $recipientCount * 3) {
            $riskScore += 15;
            $riskFactors[] = "High download frequency ({$downloads} downloads)";
        }

        // Factor 4: Recent suspicious patterns
        $suspiciousPatterns = $this->accessService->getSuspiciousPatterns(
            $job->user_id,
            24
        );
        if (!empty($suspiciousPatterns['rapid_downloads'])) {
            $riskScore += 25;
            $riskFactors[] = "Rapid successive downloads detected";
        }

        // Determine risk level
        $riskLevel = match (true) {
            $riskScore >= 60 => 'high',
            $riskScore >= 30 => 'medium',
            default => 'low',
        };

        return [
            'score' => $riskScore,
            'level' => $riskLevel,
            'factors' => $riskFactors,
            'recommendation' => match ($riskLevel) {
                'high' => 'Immediate investigation recommended. Consider revoking access.',
                'medium' => 'Monitor closely. Review access patterns regularly.',
                'low' => 'Normal risk level. Continue standard monitoring.',
            },
        ];
    }

    /**
     * Check if a marker is known in the system.
     */
    public function isKnownMarker(string $marker): bool
    {
        return DocumentFingerprint::where('unique_marker', $marker)->exists();
    }

    /**
     * Get all jobs associated with a recipient email.
     */
    public function getJobsByRecipient(string $email): Collection
    {
        return DocumentFingerprint::where('recipient_email', $email)
            ->with('watermarkJob')
            ->get()
            ->pluck('watermarkJob')
            ->filter();
    }
}
