<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreWatermarkJobRequest;
use App\Http\Resources\WatermarkJobResource;
use App\Http\Resources\WatermarkJobCollection;
use App\Jobs\ProcessWatermarkPdf;
use App\Models\WatermarkJob;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Group('Watermark Jobs', 'Create and manage PDF watermarking jobs')]
class WatermarkJobController extends Controller
{
    /**
     * List watermark jobs
     *
     * Retrieve a paginated list of the authenticated user's watermark jobs,
     * ordered by creation date (newest first).
     *
     * @queryParam per_page integer Number of jobs per page. Default: 15. Example: 10
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "original_filename": "document.pdf",
     *       "status": "done",
     *       "error_message": null,
     *       "settings": {
     *         "iso": "ISO-12345",
     *         "lender": "ACME Corp",
     *         "font_size": 15,
     *         "color": "#878787",
     *         "opacity": 33
     *       },
     *       "page_count": 5,
     *       "file_size": 1048576,
     *       "file_size_formatted": "1 MB",
     *       "can_download": true,
     *       "processed_at": "2026-01-17T10:05:00.000000Z",
     *       "created_at": "2026-01-17T10:00:00.000000Z",
     *       "updated_at": "2026-01-17T10:05:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 15,
     *     "total": 1
     *   }
     * }
     */
    public function index(Request $request): WatermarkJobCollection
    {
        $jobs = $request->user()
            ->watermarkJobs()
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return new WatermarkJobCollection($jobs);
    }

    /**
     * Create watermark job
     *
     * Upload a PDF file and create a new watermark job. The job will be processed
     * asynchronously in the background. Use the status endpoint to poll for completion.
     *
     * **Watermark Settings:**
     * - `iso` - The ISO number to display in the watermark
     * - `lender` - The lender name to display in the watermark
     * - `font_size` - Font size in points (8-48, default: 15)
     * - `color` - Hex color code for the watermark text (default: #878787)
     * - `opacity` - Opacity percentage 1-100 (default: 33)
     *
     * @response 201 {
     *   "message": "Watermark job created successfully",
     *   "data": {
     *     "id": 1,
     *     "original_filename": "document.pdf",
     *     "status": "pending",
     *     "error_message": null,
     *     "settings": {
     *       "iso": "ISO-12345",
     *       "lender": "ACME Corp",
     *       "font_size": 15,
     *       "color": "#878787",
     *       "opacity": 33
     *     },
     *     "page_count": null,
     *     "file_size": 1048576,
     *     "file_size_formatted": "1 MB",
     *     "can_download": false,
     *     "processed_at": null,
     *     "created_at": "2026-01-17T10:00:00.000000Z",
     *     "updated_at": "2026-01-17T10:00:00.000000Z"
     *   }
     * }
     * @response 403 scenario="Job limit exceeded" {
     *   "message": "You have reached your daily job limit. Please upgrade your plan or wait until tomorrow.",
     *   "error": "job_limit_exceeded"
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "The pdf file field is required.",
     *   "errors": {
     *     "pdf_file": ["The pdf file field is required."]
     *   }
     * }
     */
    public function store(StoreWatermarkJobRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check user permissions/limits
        $canCreate = $user->canCreateJob();
        if (!$canCreate['allowed']) {
            return response()->json([
                'message' => $canCreate['reason'],
                'error' => 'job_limit_exceeded',
            ], 403);
        }

        // Store the PDF file
        $pdfFile = $request->file('pdf_file');
        $originalFilename = $pdfFile->getClientOriginalName();
        $uploadPath = config('watermark.paths.uploads', 'private/watermark/uploads');
        $storedPath = $pdfFile->storeAs(
            $uploadPath,
            Str::uuid() . '.pdf'
        );

        // Create the watermark job
        $watermarkJob = WatermarkJob::create([
            'user_id' => $user->id,
            'original_filename' => $originalFilename,
            'original_path' => $storedPath,
            'status' => WatermarkJob::STATUS_PENDING,
            'settings' => $request->getWatermarkSettings(),
            'file_size' => $pdfFile->getSize(),
        ]);

        // Dispatch the processing job
        ProcessWatermarkPdf::dispatch($watermarkJob);

        // Deduct credits if necessary
        if ($canCreate['use_credits'] ?? false) {
            $user->useCredits(1, "Watermark job #{$watermarkJob->id}");
        }

        return response()->json([
            'message' => 'Watermark job created successfully',
            'data' => new WatermarkJobResource($watermarkJob),
        ], 201);
    }

    /**
     * Get watermark job
     *
     * Retrieve details of a specific watermark job. Only jobs owned by the
     * authenticated user can be accessed.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "original_filename": "document.pdf",
     *     "status": "done",
     *     "error_message": null,
     *     "settings": {
     *       "iso": "ISO-12345",
     *       "lender": "ACME Corp",
     *       "font_size": 15,
     *       "color": "#878787",
     *       "opacity": 33
     *     },
     *     "page_count": 5,
     *     "file_size": 1048576,
     *     "file_size_formatted": "1 MB",
     *     "can_download": true,
     *     "processed_at": "2026-01-17T10:05:00.000000Z",
     *     "created_at": "2026-01-17T10:00:00.000000Z",
     *     "updated_at": "2026-01-17T10:05:00.000000Z"
     *   }
     * }
     * @response 403 scenario="Not authorized" {
     *   "message": "You are not authorized to access this job."
     * }
     * @response 404 scenario="Not found" {
     *   "message": "No query results for model [WatermarkJob]."
     * }
     */
    public function show(Request $request, WatermarkJob $watermarkJob): JsonResponse
    {
        $this->authorizeJob($request, $watermarkJob);

        return response()->json([
            'data' => new WatermarkJobResource($watermarkJob),
        ]);
    }

    /**
     * Delete watermark job
     *
     * Delete a watermark job and its associated files (both the original
     * uploaded PDF and the watermarked output). This action cannot be undone.
     *
     * @response 200 {
     *   "message": "Watermark job deleted successfully"
     * }
     * @response 403 scenario="Not authorized" {
     *   "message": "You are not authorized to access this job."
     * }
     * @response 404 scenario="Not found" {
     *   "message": "No query results for model [WatermarkJob]."
     * }
     */
    public function destroy(Request $request, WatermarkJob $watermarkJob): JsonResponse
    {
        $this->authorizeJob($request, $watermarkJob);

        $watermarkJob->deleteFiles();
        $watermarkJob->delete();

        return response()->json([
            'message' => 'Watermark job deleted successfully',
        ]);
    }

    /**
     * Get job status
     *
     * Check the processing status of a watermark job. Use this endpoint to poll
     * for job completion after creating a job.
     *
     * **Possible status values:**
     * - `pending` - Job is queued and waiting to be processed
     * - `processing` - Job is currently being processed
     * - `done` - Job completed successfully, file is ready for download
     * - `failed` - Job failed, check `error_message` for details
     *
     * @response 200 scenario="Processing" {
     *   "id": 1,
     *   "status": "processing",
     *   "error_message": null,
     *   "can_download": false,
     *   "processed_at": null
     * }
     * @response 200 scenario="Completed" {
     *   "id": 1,
     *   "status": "done",
     *   "error_message": null,
     *   "can_download": true,
     *   "processed_at": "2026-01-17T10:05:00.000000Z"
     * }
     * @response 200 scenario="Failed" {
     *   "id": 1,
     *   "status": "failed",
     *   "error_message": "Failed to process PDF: Invalid file format",
     *   "can_download": false,
     *   "processed_at": "2026-01-17T10:05:00.000000Z"
     * }
     * @response 403 scenario="Not authorized" {
     *   "message": "You are not authorized to access this job."
     * }
     */
    public function status(Request $request, WatermarkJob $watermarkJob): JsonResponse
    {
        $this->authorizeJob($request, $watermarkJob);

        return response()->json([
            'id' => $watermarkJob->id,
            'status' => $watermarkJob->status,
            'error_message' => $watermarkJob->error_message,
            'can_download' => $watermarkJob->isDone() && $watermarkJob->outputExists(),
            'processed_at' => $watermarkJob->processed_at?->toIso8601String(),
        ]);
    }

    /**
     * Download watermarked PDF
     *
     * Download the processed watermarked PDF file. The job must be in `done` status
     * and the output file must exist. The response will be a binary PDF stream.
     *
     * @response 200 scenario="Success" The watermarked PDF file (application/pdf)
     * @response 403 scenario="Not authorized" {
     *   "message": "You are not authorized to access this job."
     * }
     * @response 404 scenario="Not ready" {
     *   "message": "File not available for download",
     *   "error": "file_not_ready"
     * }
     */
    public function download(Request $request, WatermarkJob $watermarkJob): StreamedResponse|JsonResponse
    {
        $this->authorizeJob($request, $watermarkJob);

        if (!$watermarkJob->isDone() || !$watermarkJob->outputExists()) {
            return response()->json([
                'message' => 'File not available for download',
                'error' => 'file_not_ready',
            ], 404);
        }

        return Storage::download(
            $watermarkJob->output_path,
            $watermarkJob->getOutputFilename()
        );
    }

    /**
     * Authorize that the user owns the job.
     */
    private function authorizeJob(Request $request, WatermarkJob $watermarkJob): void
    {
        if ($watermarkJob->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to access this job.');
        }
    }
}
