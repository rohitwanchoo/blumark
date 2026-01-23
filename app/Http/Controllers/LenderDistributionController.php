<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWatermarkPdf;
use App\Mail\LenderDocumentMail;
use App\Models\EmailTemplate;
use App\Models\Lender;
use App\Models\LenderDistribution;
use App\Models\LenderDistributionItem;
use App\Models\WatermarkJob;
use App\Services\CustomSmtpMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class LenderDistributionController extends Controller
{
    public function index()
    {
        $distributions = Auth::user()->lenderDistributions()
            ->with('items')
            ->recent()
            ->paginate(20);

        return view('distributions.index', compact('distributions'));
    }

    public function create()
    {
        $user = Auth::user();

        // Check if user has set their ISO/company name
        $hasIsoName = !empty($user->company_name);

        $lenders = $user->lenders()->active()->orderBy('company_name')->get();
        $emailTemplates = $user->emailTemplates()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
        $smtpSettings = $user->smtpSettings()->latest()->get();

        return view('distributions.create', compact('lenders', 'emailTemplates', 'smtpSettings', 'hasIsoName'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user has set their ISO/company name
        if (empty($user->company_name)) {
            return back()->withErrors(['iso' => 'Please update your ISO/Company name in your Profile before creating submissions.']);
        }

        $validated = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:pdf|max:' . (config('watermark.max_upload_mb', 50) * 1024),
            'lender_ids' => 'required|array|min:1',
            'lender_ids.*' => 'exists:lenders,id',
            'name' => 'nullable|string|max:255',
            'font_size' => 'nullable|integer|min:8|max:72',
            'color' => 'nullable|string|max:7',
            'opacity' => 'nullable|integer|min:1|max:100',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'smtp_setting_id' => 'nullable|exists:smtp_settings,id',
        ]);
        $files = $request->file('files');
        $fileCount = count($files);

        // Check billing limits (1 distribution = 1 job)
        $canCreate = $user->canCreateJob();
        if (!$canCreate['allowed']) {
            return back()->withErrors(['files' => $canCreate['reason']]);
        }
        $useCredits = $canCreate['use_credits'] ?? false;

        // Verify lenders belong to user
        $lenderIds = $validated['lender_ids'];
        $lenders = $user->lenders()->whereIn('id', $lenderIds)->get();

        if ($lenders->count() !== count($lenderIds)) {
            return back()->withErrors(['lender_ids' => 'Invalid lenders selected.']);
        }

        // Store all source files
        $uploadPath = config('watermark.paths.uploads', 'private/watermark/uploads');
        $sourceFiles = [];

        foreach ($files as $file) {
            $filename = Str::uuid() . '.pdf';
            $storagePath = $file->storeAs($uploadPath, $filename);
            $sourceFiles[] = [
                'filename' => $file->getClientOriginalName(),
                'path' => $storagePath,
                'size' => $file->getSize(),
            ];
        }

        // Build settings
        $settings = [
            'type' => 'iso_lender',
            'font_size' => $validated['font_size'] ?? config('watermark.defaults.font_size', 15),
            'color' => $validated['color'] ?? config('watermark.defaults.color', '#878787'),
            'opacity' => $validated['opacity'] ?? config('watermark.defaults.opacity', 10),
            'iso' => $user->company_name ?? $user->name,
        ];

        // Build distribution name
        $distributionName = $validated['name'];
        if (empty($distributionName)) {
            if ($fileCount === 1) {
                $distributionName = $sourceFiles[0]['filename'];
            } else {
                $distributionName = $fileCount . ' Documents - ' . now()->format('M j, Y');
            }
        }

        // Total items = files × lenders
        $totalItems = $fileCount * $lenders->count();

        // Create the distribution record
        $distribution = LenderDistribution::create([
            'user_id' => $user->id,
            'name' => $distributionName,
            'source_filename' => $fileCount === 1 ? $sourceFiles[0]['filename'] : "{$fileCount} files",
            'source_path' => $fileCount === 1 ? $sourceFiles[0]['path'] : null,
            'source_files' => $sourceFiles,
            'settings' => $settings,
            'email_template_id' => $validated['email_template_id'] ?? null,
            'smtp_setting_id' => $validated['smtp_setting_id'] ?? null,
            'status' => 'pending',
            'total_lenders' => $totalItems, // Actually total items (files × lenders)
        ]);

        // Use credits if needed (1 distribution = 1 job)
        if ($useCredits) {
            $user->useCredits(1, "Lender distribution #{$distribution->id} ({$lenders->count()} lenders, {$fileCount} files)");
        }

        // Create distribution items and watermark jobs for each file × lender combination
        foreach ($sourceFiles as $fileIndex => $sourceFile) {
            foreach ($lenders as $lender) {
                // Create watermark job with lender-specific settings
                $jobSettings = array_merge($settings, [
                    'lender' => $lender->company_name,
                ]);

                $watermarkJob = WatermarkJob::create([
                    'user_id' => $user->id,
                    'original_filename' => $sourceFile['filename'],
                    'original_path' => $sourceFile['path'],
                    'status' => 'pending',
                    'settings' => $jobSettings,
                    'file_size' => $sourceFile['size'],
                ]);

                // Create distribution item
                LenderDistributionItem::create([
                    'lender_distribution_id' => $distribution->id,
                    'lender_id' => $lender->id,
                    'watermark_job_id' => $watermarkJob->id,
                    'lender_snapshot' => $lender->toSnapshotArray(),
                    'source_file_index' => $fileIndex,
                    'status' => 'pending',
                ]);

                // Dispatch processing job
                ProcessWatermarkPdf::dispatch($watermarkJob);
            }
        }

        $distribution->markAsProcessing();

        return redirect()->route('distributions.show', $distribution)
            ->with('success', "Distribution created. Processing {$fileCount} " . ($fileCount === 1 ? 'document' : 'documents') . " for {$lenders->count()} " . ($lenders->count() === 1 ? 'lender' : 'lenders') . ".");
    }

    public function show(LenderDistribution $distribution)
    {
        $this->authorize('view', $distribution);

        $distribution->load(['items.watermarkJob', 'items.lender', 'emailTemplate']);

        return view('distributions.show', compact('distribution'));
    }

    public function status(LenderDistribution $distribution)
    {
        $this->authorize('view', $distribution);

        $distribution->load('items.watermarkJob');

        $items = $distribution->items->map(function ($item) {
            return [
                'id' => $item->id,
                'lender_name' => $item->getLenderCompanyName(),
                'source_filename' => $item->getSourceFilename(),
                'source_file_index' => $item->source_file_index,
                'status' => $item->watermarkJob?->status ?? $item->status,
                'sent' => $item->isSent(),
                'sent_via' => $item->sent_via,
                'can_download' => $item->canDownload(),
                'can_send' => $item->canSend(),
            ];
        });

        return response()->json([
            'status' => $distribution->status,
            'progress' => $distribution->getProgressPercentage(),
            'processed_count' => $distribution->processed_count,
            'failed_count' => $distribution->failed_count,
            'total_lenders' => $distribution->total_lenders,
            'items' => $items,
        ]);
    }

    public function destroy(LenderDistribution $distribution)
    {
        $this->authorize('delete', $distribution);

        // Delete source files (handle both legacy single file and multiple files)
        $sourceFiles = $distribution->getSourceFilesArray();
        foreach ($sourceFiles as $sourceFile) {
            if (!empty($sourceFile['path']) && Storage::exists($sourceFile['path'])) {
                Storage::delete($sourceFile['path']);
            }
        }

        // Delete output files from watermark jobs
        foreach ($distribution->items as $item) {
            if ($item->watermarkJob) {
                $item->watermarkJob->deleteFiles();
            }
        }

        $distribution->delete();

        return redirect()->route('distributions.index')
            ->with('success', 'Distribution deleted successfully.');
    }

    public function itemDownload(LenderDistribution $distribution, LenderDistributionItem $item)
    {
        $this->authorize('view', $distribution);

        if (!$item->canDownload()) {
            return back()->withErrors(['error' => 'File not ready for download.']);
        }

        $outputPath = $item->watermarkJob->getOutputFullPath();

        if (!$outputPath || !file_exists($outputPath)) {
            return back()->withErrors(['error' => 'Output file not found.']);
        }

        // Use the specific source file name for this item
        $sourceFilename = $item->getSourceFilename();
        $filename = pathinfo($sourceFilename, PATHINFO_FILENAME)
            . '-' . Str::slug($item->getLenderCompanyName())
            . '-watermarked.pdf';

        return response()->download($outputPath, $filename);
    }

    public function itemSend(Request $request, LenderDistribution $distribution, LenderDistributionItem $item)
    {
        $this->authorize('view', $distribution);

        $validated = $request->validate([
            'send_via' => 'required|in:email_attachment,email_link',
            'email' => 'nullable|email',
            'template_id' => 'nullable|exists:email_templates,id',
        ]);

        if (!$item->canSend()) {
            return back()->withErrors(['error' => 'Cannot send email. File not ready or no email address.']);
        }

        $email = $validated['email'] ?? $item->getLenderEmail();

        if (!$email) {
            return back()->withErrors(['error' => 'No email address provided.']);
        }

        $sendVia = $validated['send_via'];
        $user = Auth::user();

        // Get email template: request override > distribution template > user default
        $template = null;
        if (!empty($validated['template_id'])) {
            $template = $user->emailTemplates()->find($validated['template_id']);
        } elseif ($distribution->email_template_id) {
            $template = $distribution->emailTemplate;
        } else {
            $template = EmailTemplate::getDefaultForUser($user->id);
        }

        // Get SMTP settings to determine from address
        $fromEmail = null;
        $fromName = null;
        if ($distribution->smtp_setting_id) {
            $smtpSetting = \App\Models\SmtpSetting::where('id', $distribution->smtp_setting_id)
                ->where('user_id', $user->id)
                ->first();
            if ($smtpSetting) {
                $fromEmail = $smtpSetting->from_email;
                $fromName = $smtpSetting->from_name;
            }
        } else {
            $smtpSetting = \App\Models\SmtpSetting::getActiveForUser($user->id);
            if ($smtpSetting) {
                $fromEmail = $smtpSetting->from_email;
                $fromName = $smtpSetting->from_name;
            }
        }

        // Send the email using custom SMTP if configured
        CustomSmtpMailer::sendWithCustomSmtp(
            $user->id,
            new LenderDocumentMail(
                distribution: $distribution,
                items: collect([$item]),
                senderName: $user->getFullName(),
                senderCompany: $user->company_name ?? $user->name,
                attachPdf: $sendVia === 'email_attachment',
                template: $template,
                fromEmail: $fromEmail,
                fromName: $fromName,
            ),
            $email,
            $distribution->smtp_setting_id
        );

        $item->markAsSent($sendVia);

        return back()->with('success', "Document sent to {$email} successfully.");
    }

    public function sendAll(Request $request, LenderDistribution $distribution)
    {
        $this->authorize('view', $distribution);

        $validated = $request->validate([
            'send_via' => 'required|in:email_attachment,email_link',
            'template_id' => 'nullable|exists:email_templates,id',
        ]);

        $sendVia = $validated['send_via'];
        $user = Auth::user();

        // Get email template: request override > distribution template > user default
        $template = null;
        if (!empty($validated['template_id'])) {
            $template = $user->emailTemplates()->find($validated['template_id']);
        } elseif ($distribution->email_template_id) {
            $template = $distribution->emailTemplate;
        } else {
            $template = EmailTemplate::getDefaultForUser($user->id);
        }

        // Get SMTP settings to determine from address
        $fromEmail = null;
        $fromName = null;
        if ($distribution->smtp_setting_id) {
            $smtpSetting = \App\Models\SmtpSetting::where('id', $distribution->smtp_setting_id)
                ->where('user_id', $user->id)
                ->first();
            if ($smtpSetting) {
                $fromEmail = $smtpSetting->from_email;
                $fromName = $smtpSetting->from_name;
            }
        } else {
            $smtpSetting = \App\Models\SmtpSetting::getActiveForUser($user->id);
            if ($smtpSetting) {
                $fromEmail = $smtpSetting->from_email;
                $fromName = $smtpSetting->from_name;
            }
        }

        // Group items by lender email (only items that can be sent and haven't been sent)
        $itemsByLender = $distribution->items
            ->filter(fn($item) => $item->canSend() && !$item->isSent())
            ->groupBy(fn($item) => $item->getLenderEmail());

        $lenderCount = 0;

        foreach ($itemsByLender as $email => $lenderItems) {
            // Send ONE consolidated email with all documents for this lender using custom SMTP if configured
            CustomSmtpMailer::sendWithCustomSmtp(
                $user->id,
                new LenderDocumentMail(
                    distribution: $distribution,
                    items: $lenderItems,
                    senderName: $user->getFullName(),
                    senderCompany: $user->company_name ?? $user->name,
                    attachPdf: $sendVia === 'email_attachment',
                    template: $template,
                    fromEmail: $fromEmail,
                    fromName: $fromName,
                ),
                $email,
                $distribution->smtp_setting_id
            );

            // Mark all items for this lender as sent
            foreach ($lenderItems as $item) {
                $item->markAsSent($sendVia);
            }

            $lenderCount++;
        }

        return back()->with('success', "Sent documents to {$lenderCount} " . ($lenderCount === 1 ? 'lender' : 'lenders') . ".");
    }

    public function downloadAll(LenderDistribution $distribution)
    {
        $this->authorize('view', $distribution);

        $items = $distribution->items->filter(fn($item) => $item->canDownload());

        if ($items->isEmpty()) {
            return back()->withErrors(['error' => 'No files ready for download.']);
        }

        // Create ZIP file
        $zipFilename = Str::slug($distribution->name ?? 'distribution') . '-all.zip';
        $zipPath = storage_path('app/temp/' . Str::uuid() . '.zip');

        // Ensure temp directory exists
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return back()->withErrors(['error' => 'Could not create ZIP file.']);
        }

        foreach ($items as $item) {
            $outputPath = $item->watermarkJob->getOutputFullPath();
            if ($outputPath && file_exists($outputPath)) {
                $sourceFilename = $item->getSourceFilename();
                $filename = pathinfo($sourceFilename, PATHINFO_FILENAME)
                    . '-' . Str::slug($item->getLenderCompanyName())
                    . '-watermarked.pdf';
                $zip->addFile($outputPath, $filename);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }
}
