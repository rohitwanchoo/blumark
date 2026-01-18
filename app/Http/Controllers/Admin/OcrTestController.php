<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OcrTestResult;
use App\Models\WatermarkJob;
use App\Services\Ocr\OcrManager;
use Illuminate\Http\Request;

class OcrTestController extends Controller
{
    public function __construct(
        protected OcrManager $ocrManager
    ) {}

    /**
     * Show OCR test page.
     */
    public function index()
    {
        $availableEngines = $this->ocrManager->getAvailableEngines();
        $recentTests = OcrTestResult::with('watermarkJob')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.ocr.index', compact('availableEngines', 'recentTests'));
    }

    /**
     * Run OCR test on a watermarked job.
     */
    public function test(Request $request, WatermarkJob $job)
    {
        $engine = $request->input('engine');
        $patterns = $request->input('patterns', []);

        if (!$job->output_path || !file_exists(storage_path('app/' . $job->output_path))) {
            return back()->with('error', 'Watermarked PDF not found for this job.');
        }

        $pdfPath = storage_path('app/' . $job->output_path);

        try {
            $ocrResult = $this->ocrManager->extractText($pdfPath, ['engine' => $engine]);

            // Check if watermark text is detected
            $settings = $job->settings ?? [];
            $watermarkText = $settings['iso'] ?? $settings['text'] ?? null;
            $watermarkDetected = false;

            if ($watermarkText) {
                $watermarkDetected = $ocrResult->containsPattern($watermarkText);
            }

            // Store test result
            $testResult = OcrTestResult::create([
                'watermark_job_id' => $job->id,
                'ocr_engine' => $engine ?? $this->ocrManager->getDefault()->getEngine(),
                'extracted_text' => $ocrResult->text,
                'watermark_detected' => $watermarkDetected,
                'confidence_score' => $ocrResult->confidence,
                'processing_time_ms' => $ocrResult->processingTimeMs,
                'test_config' => [
                    'patterns' => $patterns,
                    'expected_watermark' => $watermarkText,
                    'pages_processed' => $ocrResult->pagesProcessed,
                ],
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'result' => $testResult,
                    'ocr_result' => $ocrResult->toArray(),
                ]);
            }

            return view('admin.ocr.result', [
                'job' => $job,
                'testResult' => $testResult,
                'ocrResult' => $ocrResult,
                'watermarkText' => $watermarkText,
            ]);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'OCR test failed: ' . $e->getMessage());
        }
    }

    /**
     * Compare OCR results across engines.
     */
    public function compare(Request $request, WatermarkJob $job)
    {
        if (!$job->output_path || !file_exists(storage_path('app/' . $job->output_path))) {
            return back()->with('error', 'Watermarked PDF not found for this job.');
        }

        $pdfPath = storage_path('app/' . $job->output_path);
        $patterns = $request->input('patterns', []);

        // Add watermark text as pattern
        $settings = $job->settings ?? [];
        $watermarkText = $settings['iso'] ?? $settings['text'] ?? null;
        if ($watermarkText && !in_array($watermarkText, $patterns)) {
            $patterns[] = $watermarkText;
        }

        try {
            $results = $this->ocrManager->compareEngines($pdfPath, $patterns);

            // Store results for each engine
            foreach ($results as $engineName => $result) {
                if ($result['available']) {
                    OcrTestResult::create([
                        'watermark_job_id' => $job->id,
                        'ocr_engine' => $engineName,
                        'extracted_text' => $result['text'] ?? '',
                        'watermark_detected' => !empty($result['patterns_found']),
                        'confidence_score' => $result['confidence'] ?? 0,
                        'processing_time_ms' => $result['processing_time_ms'] ?? 0,
                        'test_config' => [
                            'patterns' => $patterns,
                            'comparison_test' => true,
                        ],
                    ]);
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'results' => $results,
                ]);
            }

            return view('admin.ocr.compare', [
                'job' => $job,
                'results' => $results,
                'patterns' => $patterns,
            ]);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'OCR comparison failed: ' . $e->getMessage());
        }
    }

    /**
     * Batch test multiple documents.
     */
    public function batchTest(Request $request)
    {
        $request->validate([
            'job_ids' => 'required|array',
            'job_ids.*' => 'exists:watermark_jobs,id',
            'engine' => 'nullable|string',
        ]);

        $jobIds = $request->input('job_ids');
        $engine = $request->input('engine');
        $results = [];

        foreach ($jobIds as $jobId) {
            $job = WatermarkJob::find($jobId);
            if (!$job || !$job->output_path) {
                $results[$jobId] = ['error' => 'Job not found or no output'];
                continue;
            }

            $pdfPath = storage_path('app/' . $job->output_path);
            if (!file_exists($pdfPath)) {
                $results[$jobId] = ['error' => 'PDF file not found'];
                continue;
            }

            try {
                $ocrResult = $this->ocrManager->extractText($pdfPath, ['engine' => $engine]);

                $settings = $job->settings ?? [];
                $watermarkText = $settings['iso'] ?? $settings['text'] ?? null;
                $watermarkDetected = $watermarkText ? $ocrResult->containsPattern($watermarkText) : null;

                $testResult = OcrTestResult::create([
                    'watermark_job_id' => $job->id,
                    'ocr_engine' => $engine ?? $this->ocrManager->getDefault()->getEngine(),
                    'extracted_text' => $ocrResult->text,
                    'watermark_detected' => $watermarkDetected,
                    'confidence_score' => $ocrResult->confidence,
                    'processing_time_ms' => $ocrResult->processingTimeMs,
                    'test_config' => ['batch_test' => true],
                ]);

                $results[$jobId] = [
                    'success' => true,
                    'watermark_detected' => $watermarkDetected,
                    'confidence' => $ocrResult->confidence,
                    'word_count' => $ocrResult->getWordCount(),
                ];

            } catch (\Exception $e) {
                $results[$jobId] = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['results' => $results]);
        }

        return view('admin.ocr.batch-results', [
            'results' => $results,
            'jobs' => WatermarkJob::whereIn('id', $jobIds)->get()->keyBy('id'),
        ]);
    }

    /**
     * View test history for a job.
     */
    public function history(WatermarkJob $job)
    {
        $tests = OcrTestResult::where('watermark_job_id', $job->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ocr.history', [
            'job' => $job,
            'tests' => $tests,
        ]);
    }

    /**
     * Delete a test result.
     */
    public function destroy(OcrTestResult $result)
    {
        $result->delete();

        return back()->with('success', 'Test result deleted.');
    }
}
