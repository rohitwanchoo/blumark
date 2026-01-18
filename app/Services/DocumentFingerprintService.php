<?php

namespace App\Services;

use App\Models\DocumentFingerprint;
use App\Models\WatermarkJob;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class DocumentFingerprintService
{
    /**
     * Generate a fingerprint for a watermarked document.
     */
    public function generateFingerprint(
        WatermarkJob $job,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $recipientId = null
    ): DocumentFingerprint {
        // Generate unique marker for recipient tracking
        $uniqueMarker = $this->generateUniqueMarker($recipientId ?? $recipientEmail ?? $job->id);

        // Generate verification token
        $verificationToken = $this->generateVerificationToken();

        // Calculate document fingerprint hash
        $fingerprintHash = $this->calculateFingerprintHash($job);

        // Calculate output file hash for upload verification
        $outputFileHash = null;
        if ($job->output_path && file_exists(storage_path('app/' . $job->output_path))) {
            $outputFileHash = $this->hashFile(storage_path('app/' . $job->output_path));
        }

        // Prepare encrypted metadata
        $metadata = [
            'job_id' => $job->id,
            'original_filename' => $job->original_filename,
            'watermark_settings' => $job->settings,
            'created_at' => now()->toIso8601String(),
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'unique_marker' => $uniqueMarker,
        ];

        return DocumentFingerprint::create([
            'watermark_job_id' => $job->id,
            'fingerprint_hash' => $fingerprintHash,
            'output_file_hash' => $outputFileHash,
            'recipient_id' => $recipientId,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'unique_marker' => $uniqueMarker,
            'metadata_json' => $metadata,
            'verification_token' => $verificationToken,
        ]);
    }

    /**
     * Verify a document by its verification token.
     */
    public function verifyDocument(string $token): array
    {
        $fingerprint = DocumentFingerprint::where('verification_token', $token)->first();

        if (!$fingerprint) {
            return [
                'valid' => false,
                'status' => 'invalid',
                'message' => 'Document not found or invalid token.',
            ];
        }

        // Update verification timestamp
        $fingerprint->update([
            'verified_at' => $fingerprint->verified_at ?? now(),
            'last_verified_at' => now(),
        ]);

        // Check if document still exists
        $job = $fingerprint->watermarkJob;
        if (!$job) {
            return [
                'valid' => false,
                'status' => 'orphaned',
                'message' => 'Original document record not found.',
                'fingerprint' => $fingerprint,
            ];
        }

        // Verify the document hash matches
        $currentHash = $this->calculateFingerprintHash($job);
        $hashMatch = hash_equals($fingerprint->fingerprint_hash, $currentHash);

        return [
            'valid' => true,
            'status' => $hashMatch ? 'verified' : 'modified',
            'message' => $hashMatch
                ? 'Document verified successfully.'
                : 'Document has been modified since watermarking.',
            'fingerprint' => $fingerprint,
            'job' => $job,
            'issued_at' => $fingerprint->created_at,
            'recipient' => $fingerprint->recipient_email,
            'hash_match' => $hashMatch,
        ];
    }

    /**
     * Verify a document by uploading the PDF and checking its hash.
     */
    public function verifyUploadedDocument(string $pdfPath): array
    {
        $uploadedHash = $this->hashFile($pdfPath);

        // Try to find a matching fingerprint by output file hash
        $fingerprint = DocumentFingerprint::where('output_file_hash', $uploadedHash)->first();

        if (!$fingerprint) {
            // Check if it's a modified version by extracting the marker
            $marker = $this->extractUniqueMarker($pdfPath);
            if ($marker) {
                $fingerprint = DocumentFingerprint::where('unique_marker', $marker)->first();
                if ($fingerprint) {
                    return [
                        'valid' => true,
                        'status' => 'modified',
                        'message' => 'Document found but has been modified since original watermarking.',
                        'fingerprint' => $fingerprint,
                        'job' => $fingerprint->watermarkJob,
                    ];
                }
            }

            return [
                'valid' => false,
                'status' => 'not_found',
                'message' => 'No matching document found in system.',
            ];
        }

        $fingerprint->update([
            'verified_at' => $fingerprint->verified_at ?? now(),
            'last_verified_at' => now(),
        ]);

        return [
            'valid' => true,
            'status' => 'verified',
            'message' => 'Document verified successfully.',
            'fingerprint' => $fingerprint,
            'job' => $fingerprint->watermarkJob,
            'issued_at' => $fingerprint->created_at,
            'recipient' => $fingerprint->recipient_email,
        ];
    }

    /**
     * Extract unique marker from a PDF file.
     * This looks for the embedded marker in the document.
     */
    public function extractUniqueMarker(string $pdfPath): ?string
    {
        try {
            // Read PDF content and look for our marker pattern
            $content = file_get_contents($pdfPath);

            // Look for marker in PDF metadata or custom dictionary
            // Pattern: /WatermarkMarker (MARKER_VALUE)
            if (preg_match('/\/WatermarkMarker\s*\(([A-Z0-9]+)\)/', $content, $matches)) {
                return $matches[1];
            }

            // Also check for marker in document info
            if (preg_match('/\/Producer\s*\(WM-([A-Z0-9]+)\)/', $content, $matches)) {
                return $matches[1];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate a unique marker for recipient tracking.
     */
    public function generateUniqueMarker(?string $seed = null): string
    {
        $seed = $seed ?? Str::uuid()->toString();
        $timestamp = now()->timestamp;

        // Create a unique, short marker
        $data = $seed . $timestamp . config('app.key');
        $hash = hash('sha256', $data);

        // Take first 12 characters and make uppercase
        return strtoupper(substr($hash, 0, 12));
    }

    /**
     * Generate a verification token.
     */
    public function generateVerificationToken(): string
    {
        return Str::random(64);
    }

    /**
     * Calculate the fingerprint hash for a watermark job.
     */
    public function calculateFingerprintHash(WatermarkJob $job): string
    {
        $components = [
            $job->id,
            $job->original_filename,
            json_encode($job->settings),
            $job->created_at->timestamp,
        ];

        // Include file hash if output exists
        if ($job->output_path && file_exists(storage_path('app/' . $job->output_path))) {
            $components[] = $this->hashFile(storage_path('app/' . $job->output_path));
        }

        return hash('sha256', implode('|', $components));
    }

    /**
     * Hash a file using SHA-256.
     */
    public function hashFile(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    /**
     * Get verification URL for a fingerprint.
     */
    public function getVerificationUrl(DocumentFingerprint $fingerprint): string
    {
        $baseUrl = config('watermark.verification.url_base', url('/verify'));
        return $baseUrl . '/' . $fingerprint->verification_token;
    }

    /**
     * Generate QR code data for verification.
     */
    public function generateQrData(DocumentFingerprint $fingerprint): array
    {
        $data = [
            'url' => $this->getVerificationUrl($fingerprint),
            'data' => [
                'doc_id' => Crypt::encryptString($fingerprint->id),
                'hash' => substr($fingerprint->fingerprint_hash, 0, 16),
                'issued' => $fingerprint->created_at->timestamp,
                'recipient' => $fingerprint->recipient_email
                    ? Crypt::encryptString($fingerprint->recipient_email)
                    : null,
            ],
            'sig' => $this->signData([
                $fingerprint->id,
                $fingerprint->fingerprint_hash,
                $fingerprint->created_at->timestamp,
            ]),
        ];

        return $data;
    }

    /**
     * Verify QR code data.
     */
    public function verifyQrData(array $qrData): array
    {
        if (!isset($qrData['data']['doc_id']) || !isset($qrData['sig'])) {
            return [
                'valid' => false,
                'status' => 'invalid_format',
                'message' => 'Invalid QR code format.',
            ];
        }

        try {
            $fingerprintId = Crypt::decryptString($qrData['data']['doc_id']);
            $fingerprint = DocumentFingerprint::find($fingerprintId);

            if (!$fingerprint) {
                return [
                    'valid' => false,
                    'status' => 'not_found',
                    'message' => 'Document not found.',
                ];
            }

            // Verify signature
            $expectedSig = $this->signData([
                $fingerprint->id,
                $fingerprint->fingerprint_hash,
                $fingerprint->created_at->timestamp,
            ]);

            if (!hash_equals($expectedSig, $qrData['sig'])) {
                return [
                    'valid' => false,
                    'status' => 'invalid_signature',
                    'message' => 'QR code signature verification failed.',
                ];
            }

            return $this->verifyDocument($fingerprint->verification_token);
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'status' => 'error',
                'message' => 'Failed to verify QR code: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sign data using HMAC.
     */
    protected function signData(array $data): string
    {
        $key = config('watermark.verification.encryption_key', config('app.key'));
        return hash_hmac('sha256', implode('|', $data), $key);
    }

    /**
     * Find fingerprints by recipient email.
     */
    public function findByRecipient(string $email): \Illuminate\Database\Eloquent\Collection
    {
        return DocumentFingerprint::where('recipient_email', $email)
            ->with('watermarkJob')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find fingerprint by unique marker.
     */
    public function findByMarker(string $marker): ?DocumentFingerprint
    {
        return DocumentFingerprint::where('unique_marker', $marker)->first();
    }

    /**
     * Get all fingerprints for a job.
     */
    public function getFingerprintsForJob(WatermarkJob $job): \Illuminate\Database\Eloquent\Collection
    {
        return $job->fingerprints()->orderBy('created_at', 'desc')->get();
    }
}
