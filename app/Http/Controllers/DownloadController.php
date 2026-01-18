<?php

namespace App\Http\Controllers;

use App\Models\WatermarkJob;
use App\Services\AccessTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function __construct(
        protected AccessTrackingService $trackingService
    ) {}

    /**
     * Download the watermarked PDF.
     */
    public function download(Request $request, WatermarkJob $job): StreamedResponse
    {
        // Ensure the user owns this job
        if ($job->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to download this file.');
        }

        // Ensure the job is complete
        if (!$job->isDone()) {
            abort(404, 'The watermarked file is not ready yet.');
        }

        // Ensure the output file exists
        if (!$job->outputExists()) {
            abort(404, 'The watermarked file could not be found.');
        }

        // Track the download for fraud detection
        if (config('watermark.security.track_downloads', true)) {
            $this->trackingService->logDownload($job, $request);
        }

        $outputFilename = $job->getOutputFilename();

        return Storage::download(
            $job->output_path,
            $outputFilename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $outputFilename . '"',
            ]
        );
    }

    /**
     * Preview/view the watermarked PDF inline.
     */
    public function preview(Request $request, WatermarkJob $job): StreamedResponse
    {
        // Ensure the user owns this job
        if ($job->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to view this file.');
        }

        // Ensure the job is complete
        if (!$job->isDone()) {
            abort(404, 'The watermarked file is not ready yet.');
        }

        // Ensure the output file exists
        if (!$job->outputExists()) {
            abort(404, 'The watermarked file could not be found.');
        }

        // Track the view for fraud detection
        if (config('watermark.security.track_downloads', true)) {
            $this->trackingService->logView($job, $request);
        }

        $outputFilename = $job->getOutputFilename();

        return Storage::response(
            $job->output_path,
            $outputFilename,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}
