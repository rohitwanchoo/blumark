<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWatermarkPdf;
use App\Models\BatchJob;
use App\Models\WatermarkJob;
use App\Models\WatermarkTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BatchController extends Controller
{
    /**
     * Show the batch upload form.
     */
    public function create()
    {
        $user = Auth::user();
        $templates = $user
            ->watermarkTemplates()
            ->orderByDesc('usage_count')
            ->get();

        return view('batch.create', compact('templates', 'user'));
    }

    /**
     * Store a new batch job with multiple files.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|min:1|max:50',
            'files.*' => 'required|file|mimes:pdf|max:' . (config('watermark.max_upload_mb', 50) * 1024),
            'iso' => 'required|string|max:100',
            'lender' => 'required|string|max:100',
            'font_size' => 'required|integer|min:8|max:48',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'opacity' => 'required|integer|min:1|max:100',
            'template_id' => 'nullable|exists:watermark_templates,id',
            'save_template' => 'boolean',
            'template_name' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $files = $request->file('files');

        // A batch counts as 1 job regardless of file count
        $canCreate = $user->canCreateJob();
        if (!$canCreate['allowed']) {
            return back()->withErrors([
                'files' => $canCreate['reason']
            ]);
        }
        $useCredits = $canCreate['use_credits'] ?? false;

        // Create batch job
        $settings = [
            'font_size' => (int) $request->input('font_size'),
            'color' => $request->input('color'),
            'opacity' => (int) $request->input('opacity'),
            'flatten_pdf' => true,
        ];

        $batchJob = BatchJob::create([
            'user_id' => $user->id,
            'name' => $request->input('batch_name', 'Batch ' . now()->format('M j, Y H:i')),
            'iso' => $request->input('iso'),
            'lender' => $request->input('lender'),
            'settings' => $settings,
            'status' => 'processing',
            'total_files' => count($files),
        ]);

        // Deduct 1 credit for the entire batch if over plan limit
        if ($useCredits) {
            $user->useCredits(1, "Batch job #{$batchJob->id} (" . count($files) . " files)");
        }

        // Process each file
        foreach ($files as $file) {
            $originalFilename = $file->getClientOriginalName();
            $uuid = Str::uuid()->toString();
            $storagePath = config('watermark.paths.uploads', 'private/watermark/uploads');
            $path = $file->storeAs($storagePath, "{$uuid}.pdf");

            $watermarkSettings = array_merge($settings, [
                'type' => 'iso_lender',
                'iso' => $request->input('iso'),
                'lender' => $request->input('lender'),
            ]);

            $job = WatermarkJob::create([
                'user_id' => $user->id,
                'batch_job_id' => $batchJob->id,
                'original_filename' => $originalFilename,
                'original_path' => $path,
                'status' => WatermarkJob::STATUS_PENDING,
                'settings' => $watermarkSettings,
                'file_size' => $file->getSize(),
            ]);

            // Dispatch processing job
            ProcessWatermarkPdf::dispatch($job);
        }

        // Increment template usage if used
        if ($request->filled('template_id')) {
            WatermarkTemplate::find($request->input('template_id'))?->incrementUsage();
        }

        // Save as new template if requested
        if ($request->boolean('save_template') && $request->filled('template_name')) {
            $user->watermarkTemplates()->create([
                'name' => $request->input('template_name'),
                'iso' => $request->input('iso'),
                'lender' => $request->input('lender'),
                'font_size' => $settings['font_size'],
                'color' => $settings['color'],
                'opacity' => $settings['opacity'],
            ]);
        }

        return redirect()->route('batch.show', $batchJob)
            ->with('success', count($files) . ' files queued for processing.');
    }

    /**
     * Display the batch job status.
     */
    public function show(BatchJob $batch)
    {
        $this->authorize('view', $batch);

        $batch->load('watermarkJobs');

        return view('batch.show', compact('batch'));
    }

    /**
     * Get batch status as JSON for polling.
     */
    public function status(BatchJob $batch)
    {
        $this->authorize('view', $batch);

        $batch->load('watermarkJobs:id,batch_job_id,original_filename,status');

        return response()->json([
            'id' => $batch->id,
            'status' => $batch->status,
            'total_files' => $batch->total_files,
            'processed_files' => $batch->processed_files,
            'failed_files' => $batch->failed_files,
            'progress' => $batch->getProgressPercentage(),
            'jobs' => $batch->watermarkJobs->map(fn($job) => [
                'id' => $job->id,
                'filename' => $job->original_filename,
                'status' => $job->status,
            ]),
        ]);
    }

    /**
     * Download all completed files as a ZIP.
     */
    public function download(BatchJob $batch)
    {
        $this->authorize('view', $batch);

        $completedJobs = $batch->watermarkJobs()->where('status', 'done')->get();

        if ($completedJobs->isEmpty()) {
            return back()->withErrors(['download' => 'No completed files to download.']);
        }

        $zipFilename = 'batch-' . $batch->id . '-' . Str::slug($batch->lender) . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);

        // Ensure temp directory exists
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['download' => 'Could not create ZIP file.']);
        }

        foreach ($completedJobs as $job) {
            if ($job->outputExists()) {
                $zip->addFile(
                    $job->getOutputFullPath(),
                    $job->getOutputFilename()
                );
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Display list of user's batch jobs.
     */
    public function index()
    {
        $batches = Auth::user()
            ->batchJobs()
            ->withCount('watermarkJobs')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('batch.index', compact('batches'));
    }

    /**
     * Delete a batch job and its associated files.
     */
    public function destroy(BatchJob $batch)
    {
        $this->authorize('delete', $batch);

        // Delete associated watermark jobs and their files
        foreach ($batch->watermarkJobs as $job) {
            $job->deleteFiles();
            $job->delete();
        }

        $batch->delete();

        return redirect()->route('batch.index')
            ->with('success', 'Batch deleted successfully.');
    }
}
