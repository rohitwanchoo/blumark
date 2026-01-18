<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentFingerprint;
use App\Models\VerificationAttempt;
use App\Services\DocumentFingerprintService;
use App\Services\QrWatermarkService;
use App\Services\TamperDetectionService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(
        protected DocumentFingerprintService $fingerprintService,
        protected TamperDetectionService $tamperService,
        protected QrWatermarkService $qrService
    ) {}

    /**
     * Verify a document by token.
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyByToken(string $token, Request $request)
    {
        $result = $this->fingerprintService->verifyDocument($token);

        // Log the verification attempt
        $fingerprint = $result['fingerprint'] ?? null;
        VerificationAttempt::create([
            'fingerprint_id' => $fingerprint?->id,
            'verification_token' => $token,
            'status' => $result['valid'] ? ($result['status'] ?? 'valid') : 'invalid',
            'verification_method' => VerificationAttempt::METHOD_TOKEN,
            'request_ip' => $request->ip(),
            'request_data' => [
                'user_agent' => $request->userAgent(),
                'method' => 'api_token',
            ],
        ]);

        return response()->json([
            'success' => $result['valid'],
            'status' => $result['status'] ?? 'unknown',
            'message' => $result['message'] ?? '',
            'data' => $result['valid'] ? [
                'issued_at' => $fingerprint?->created_at?->toIso8601String(),
                'recipient' => $fingerprint?->recipient_email,
                'recipient_name' => $fingerprint?->recipient_name,
                'verified_at' => $fingerprint?->verified_at?->toIso8601String(),
                'hash_match' => $result['hash_match'] ?? null,
            ] : null,
        ]);
    }

    /**
     * Verify an uploaded document.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUpload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:50000',
            'run_tamper_check' => 'boolean',
        ]);

        $file = $request->file('document');
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            $result = $this->fingerprintService->verifyUploadedDocument($fullPath);

            // Run tamper detection if requested and fingerprint found
            $tamperAnalysis = null;
            if ($request->boolean('run_tamper_check') && $result['valid'] && isset($result['fingerprint'])) {
                $tamperAnalysis = $this->tamperService->analyze($fullPath, $result['fingerprint']);
            }

            // Log the verification attempt
            $fingerprint = $result['fingerprint'] ?? null;
            VerificationAttempt::create([
                'fingerprint_id' => $fingerprint?->id,
                'verification_token' => $fingerprint?->verification_token ?? 'api_upload',
                'status' => $result['status'] ?? ($result['valid'] ? 'valid' : 'invalid'),
                'verification_method' => VerificationAttempt::METHOD_UPLOAD,
                'request_ip' => $request->ip(),
                'request_data' => [
                    'user_agent' => $request->userAgent(),
                    'filename' => $file->getClientOriginalName(),
                    'filesize' => $file->getSize(),
                    'method' => 'api_upload',
                ],
            ]);

            $response = [
                'success' => $result['valid'],
                'status' => $result['status'] ?? 'unknown',
                'message' => $result['message'] ?? '',
            ];

            if ($result['valid'] && $fingerprint) {
                $response['data'] = [
                    'verification_token' => $fingerprint->verification_token,
                    'issued_at' => $fingerprint->created_at->toIso8601String(),
                    'recipient' => $fingerprint->recipient_email,
                    'recipient_name' => $fingerprint->recipient_name,
                    'job_id' => $fingerprint->watermark_job_id,
                ];
            }

            if ($tamperAnalysis) {
                $response['tamper_analysis'] = [
                    'tampered' => $tamperAnalysis['tampered'],
                    'confidence' => round($tamperAnalysis['confidence'] * 100, 1),
                    'warnings' => $tamperAnalysis['warnings'],
                ];
            }

            return response()->json($response);
        } finally {
            @unlink($fullPath);
        }
    }

    /**
     * Verify QR code data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required',
        ]);

        $qrData = $request->input('qr_data');

        // Handle both JSON string and already parsed array
        if (is_string($qrData)) {
            $qrData = json_decode($qrData, true);
        }

        if (!$qrData || !is_array($qrData)) {
            return response()->json([
                'success' => false,
                'status' => 'invalid_format',
                'message' => 'Invalid QR code data format.',
            ], 400);
        }

        $result = $this->fingerprintService->verifyQrData($qrData);

        // Log the verification attempt
        $fingerprint = $result['fingerprint'] ?? null;
        VerificationAttempt::create([
            'fingerprint_id' => $fingerprint?->id,
            'verification_token' => $fingerprint?->verification_token ?? 'api_qr',
            'status' => $result['status'] ?? ($result['valid'] ? 'valid' : 'invalid'),
            'verification_method' => VerificationAttempt::METHOD_QR,
            'request_ip' => $request->ip(),
            'request_data' => [
                'user_agent' => $request->userAgent(),
                'method' => 'api_qr',
            ],
        ]);

        return response()->json([
            'success' => $result['valid'],
            'status' => $result['status'] ?? 'unknown',
            'message' => $result['message'] ?? '',
            'data' => $result['valid'] && $fingerprint ? [
                'issued_at' => $fingerprint->created_at->toIso8601String(),
                'recipient' => $fingerprint->recipient_email,
                'hash_match' => $result['hash_match'] ?? null,
            ] : null,
        ]);
    }

    /**
     * Run tamper detection on a document.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tamperCheck(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:50000',
            'token' => 'nullable|string',
        ]);

        $file = $request->file('document');
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            $fingerprint = null;
            if ($token = $request->input('token')) {
                $fingerprint = DocumentFingerprint::where('verification_token', $token)->first();
            }

            $report = $this->tamperService->generateReport($fullPath, $fingerprint);

            return response()->json([
                'success' => true,
                'report' => $report,
            ]);
        } finally {
            @unlink($fullPath);
        }
    }

    /**
     * Compare two documents.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function compare(Request $request)
    {
        $request->validate([
            'original' => 'required|file|mimes:pdf|max:50000',
            'suspect' => 'required|file|mimes:pdf|max:50000',
        ]);

        $originalFile = $request->file('original');
        $suspectFile = $request->file('suspect');

        $originalPath = $originalFile->store('temp', 'local');
        $suspectPath = $suspectFile->store('temp', 'local');

        $originalFullPath = storage_path('app/' . $originalPath);
        $suspectFullPath = storage_path('app/' . $suspectPath);

        try {
            $comparison = $this->tamperService->compareDocuments($originalFullPath, $suspectFullPath);

            return response()->json([
                'success' => true,
                'comparison' => [
                    'identical' => $comparison['identical'],
                    'similarity' => round($comparison['similarity'] * 100, 1),
                    'differences' => $comparison['differences'],
                ],
            ]);
        } finally {
            @unlink($originalFullPath);
            @unlink($suspectFullPath);
        }
    }

    /**
     * Get fingerprint details (requires authentication).
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function fingerprint(string $token, Request $request)
    {
        $fingerprint = DocumentFingerprint::where('verification_token', $token)
            ->with('watermarkJob')
            ->first();

        if (!$fingerprint) {
            return response()->json([
                'success' => false,
                'message' => 'Fingerprint not found.',
            ], 404);
        }

        // Check authorization - only job owner can see full details
        $user = $request->user();
        if (!$user || ($fingerprint->watermarkJob && $fingerprint->watermarkJob->user_id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'fingerprint' => [
                'id' => $fingerprint->id,
                'verification_token' => $fingerprint->verification_token,
                'unique_marker' => $fingerprint->unique_marker,
                'recipient_email' => $fingerprint->recipient_email,
                'recipient_name' => $fingerprint->recipient_name,
                'created_at' => $fingerprint->created_at->toIso8601String(),
                'verified_at' => $fingerprint->verified_at?->toIso8601String(),
                'last_verified_at' => $fingerprint->last_verified_at?->toIso8601String(),
                'job' => $fingerprint->watermarkJob ? [
                    'id' => $fingerprint->watermarkJob->id,
                    'original_filename' => $fingerprint->watermarkJob->original_filename,
                    'status' => $fingerprint->watermarkJob->status,
                ] : null,
            ],
        ]);
    }
}
