<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWatermarkJobRequest;
use App\Jobs\ProcessWatermarkPdf;
use App\Models\WatermarkJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WatermarkJobController extends Controller
{
    /**
     * Display a listing of the user's watermark jobs.
     */
    public function index(Request $request): View
    {
        $jobs = $request->user()
            ->watermarkJobs()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('jobs.index', compact('jobs'));
    }

    /**
     * Store a newly created watermark job.
     */
    public function store(StoreWatermarkJobRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Store the PDF file
        $pdfFile = $request->file('pdf_file');
        $originalFilename = $pdfFile->getClientOriginalName();
        $uploadPath = config('watermark.paths.uploads', 'private/watermark/uploads');
        $storedPath = $pdfFile->storeAs(
            $uploadPath,
            Str::uuid() . '.pdf'
        );

        // Store watermark image if provided
        $watermarkImagePath = null;
        if ($request->input('watermark_type') === 'image' && $request->hasFile('watermark_image')) {
            $imageFile = $request->file('watermark_image');
            $imagePath = config('watermark.paths.watermark_images', 'private/watermark/images');
            $watermarkImagePath = $imageFile->storeAs(
                $imagePath,
                Str::uuid() . '.' . $imageFile->getClientOriginalExtension()
            );
        }

        // Create the watermark job
        $watermarkJob = WatermarkJob::create([
            'user_id' => $user->id,
            'original_filename' => $originalFilename,
            'original_path' => $storedPath,
            'watermark_image_path' => $watermarkImagePath,
            'status' => WatermarkJob::STATUS_PENDING,
            'settings' => $request->getWatermarkSettings(),
            'file_size' => $pdfFile->getSize(),
        ]);

        // Dispatch the processing job
        ProcessWatermarkPdf::dispatch($watermarkJob);

        return redirect()
            ->route('jobs.show', $watermarkJob)
            ->with('success', 'Your PDF has been queued for watermarking. You will be notified when it\'s ready.');
    }

    /**
     * Display the specified watermark job.
     */
    public function show(Request $request, WatermarkJob $job): View|RedirectResponse
    {
        // Ensure the user owns this job
        if ($job->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to view this job.');
        }

        return view('jobs.show', compact('job'));
    }

    /**
     * Remove the specified watermark job.
     */
    public function destroy(Request $request, WatermarkJob $job): RedirectResponse
    {
        // Ensure the user owns this job
        if ($job->user_id !== $request->user()->id) {
            abort(403, 'You are not authorized to delete this job.');
        }

        // Delete associated files
        $job->deleteFiles();

        // Delete the job record
        $job->delete();

        return redirect()
            ->route('jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    /**
     * Get the status of a watermark job (for AJAX polling).
     */
    public function status(Request $request, WatermarkJob $job)
    {
        // Ensure the user owns this job
        if ($job->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $job->id,
            'status' => $job->status,
            'error_message' => $job->error_message,
            'can_download' => $job->isDone() && $job->outputExists(),
            'processed_at' => $job->processed_at?->toIso8601String(),
        ]);
    }
}
