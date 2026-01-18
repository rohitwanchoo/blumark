<?php

namespace App\Http\Controllers;

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
     * Show the verification form.
     */
    public function index()
    {
        return view('verify.index');
    }

    /**
     * Verify a document by token.
     */
    public function show(string $token, Request $request)
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
                'referrer' => $request->header('referer'),
            ],
        ]);

        if (!$result['valid']) {
            return view('verify.invalid', [
                'message' => $result['message'] ?? 'Invalid verification token.',
                'status' => $result['status'] ?? 'invalid',
            ]);
        }

        return view('verify.show', [
            'result' => $result,
            'fingerprint' => $fingerprint,
            'job' => $result['job'] ?? null,
        ]);
    }

    /**
     * Verify an uploaded document.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:50000',
        ]);

        $file = $request->file('document');
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            // Try to verify the uploaded document
            $result = $this->fingerprintService->verifyUploadedDocument($fullPath);

            // Run tamper detection only if document was modified (found by marker but hash doesn't match)
            // Skip tamper analysis when hash matches exactly (status = 'verified') - no tampering possible
            $tamperAnalysis = null;
            if ($result['valid'] && isset($result['fingerprint']) && $result['status'] === 'modified') {
                $tamperAnalysis = $this->tamperService->analyze($fullPath, $result['fingerprint']);
            }

            // Log the verification attempt
            $fingerprint = $result['fingerprint'] ?? null;
            VerificationAttempt::create([
                'fingerprint_id' => $fingerprint?->id,
                'verification_token' => $fingerprint?->verification_token ?? 'upload',
                'status' => $result['status'] ?? ($result['valid'] ? 'valid' : 'invalid'),
                'verification_method' => VerificationAttempt::METHOD_UPLOAD,
                'request_ip' => $request->ip(),
                'request_data' => [
                    'user_agent' => $request->userAgent(),
                    'filename' => $file->getClientOriginalName(),
                    'filesize' => $file->getSize(),
                ],
            ]);

            if (!$result['valid']) {
                return view('verify.invalid', [
                    'message' => $result['message'] ?? 'Document not found in system.',
                    'status' => $result['status'] ?? 'not_found',
                    'filename' => $file->getClientOriginalName(),
                ]);
            }

            return view('verify.show', [
                'result' => $result,
                'fingerprint' => $fingerprint,
                'job' => $result['job'] ?? null,
                'tamper_analysis' => $tamperAnalysis,
                'uploaded_filename' => $file->getClientOriginalName(),
            ]);
        } finally {
            // Cleanup temp file
            @unlink($fullPath);
        }
    }

    /**
     * Verify by scanning QR code.
     */
    public function qr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = json_decode($request->input('qr_data'), true);

        if (!$qrData) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid QR code data format.',
            ]);
        }

        $result = $this->fingerprintService->verifyQrData($qrData);

        // Log the verification attempt
        $fingerprint = $result['fingerprint'] ?? null;
        VerificationAttempt::create([
            'fingerprint_id' => $fingerprint?->id,
            'verification_token' => $fingerprint?->verification_token ?? 'qr',
            'status' => $result['status'] ?? ($result['valid'] ? 'valid' : 'invalid'),
            'verification_method' => VerificationAttempt::METHOD_QR,
            'request_ip' => $request->ip(),
            'request_data' => [
                'user_agent' => $request->userAgent(),
            ],
        ]);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if (!$result['valid']) {
            return view('verify.invalid', [
                'message' => $result['message'] ?? 'QR verification failed.',
                'status' => $result['status'] ?? 'invalid',
            ]);
        }

        return view('verify.show', [
            'result' => $result,
            'fingerprint' => $fingerprint,
            'job' => $result['job'] ?? null,
        ]);
    }

    /**
     * Get tamper detection report.
     */
    public function tamperReport(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:50000',
            'token' => 'nullable|string',
        ]);

        $file = $request->file('document');
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            // Find fingerprint if token provided
            $fingerprint = null;
            if ($token = $request->input('token')) {
                $fingerprint = DocumentFingerprint::where('verification_token', $token)->first();
            }

            // Generate tamper report
            $report = $this->tamperService->generateReport($fullPath, $fingerprint);

            return view('verify.tamper-report', [
                'report' => $report,
                'filename' => $file->getClientOriginalName(),
                'fingerprint' => $fingerprint,
            ]);
        } finally {
            @unlink($fullPath);
        }
    }
}
