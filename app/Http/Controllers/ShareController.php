<?php

namespace App\Http\Controllers;

use App\Mail\SharedDocumentMail;
use App\Models\SharedLink;
use App\Models\WatermarkJob;
use App\Services\AccessTrackingService;
use App\Services\CustomSmtpMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShareController extends Controller
{
    public function __construct(
        protected AccessTrackingService $trackingService
    ) {}

    /**
     * Create a shareable link for a job.
     */
    public function store(Request $request, WatermarkJob $job)
    {
        $this->authorize('view', $job);

        $validated = $request->validate([
            'recipient_email' => 'nullable|email|max:255',
            'recipient_name' => 'nullable|string|max:100',
            'expires_in' => 'required|integer|in:1,3,7,14,30', // days
            'max_downloads' => 'nullable|integer|min:1|max:100',
            'password' => 'nullable|string|min:4|max:50',
            'send_email' => 'boolean',
        ]);

        $sharedLink = SharedLink::create([
            'user_id' => Auth::id(),
            'watermark_job_id' => $job->id,
            'token' => SharedLink::generateToken(),
            'recipient_email' => $validated['recipient_email'] ?? null,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'expires_at' => now()->addDays($validated['expires_in']),
            'max_downloads' => $validated['max_downloads'] ?? null,
            'password_hash' => isset($validated['password']) ? password_hash($validated['password'], PASSWORD_DEFAULT) : null,
        ]);

        // Track the share event for fraud detection
        if (config('watermark.security.track_downloads', true)) {
            $this->trackingService->logShare($job, $request, $validated['recipient_email'] ?? null);
        }

        // Send email if requested
        if ($request->boolean('send_email') && $validated['recipient_email']) {
            try {
                // Get SMTP settings to determine from address
                $fromEmail = null;
                $fromName = null;
                $smtpSetting = \App\Models\SmtpSetting::getActiveForUser(Auth::id());
                if ($smtpSetting) {
                    $fromEmail = $smtpSetting->from_email;
                    $fromName = $smtpSetting->from_name;
                }

                CustomSmtpMailer::sendWithCustomSmtp(
                    Auth::id(),
                    new SharedDocumentMail($sharedLink, $job, Auth::user(), $fromEmail, $fromName),
                    $validated['recipient_email']
                );
            } catch (\Exception $e) {
                // Log but don't fail
                \Log::warning('Failed to send share email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'link' => $sharedLink->getUrl(),
            'token' => $sharedLink->token,
            'expires_at' => $sharedLink->expires_at->format('M j, Y'),
            'message' => 'Link created successfully.',
        ]);
    }

    /**
     * Show the shared link download page.
     */
    public function show(string $token)
    {
        $sharedLink = SharedLink::where('token', $token)->firstOrFail();

        if (!$sharedLink->isValid()) {
            return view('share.expired');
        }

        $job = $sharedLink->watermarkJob;

        return view('share.download', [
            'sharedLink' => $sharedLink,
            'job' => $job,
            'requiresPassword' => $sharedLink->hasPassword(),
        ]);
    }

    /**
     * Download the shared file.
     */
    public function download(Request $request, string $token)
    {
        $sharedLink = SharedLink::where('token', $token)->firstOrFail();

        if (!$sharedLink->isValid()) {
            return view('share.expired');
        }

        // Verify password if required
        if ($sharedLink->hasPassword()) {
            $request->validate(['password' => 'required|string']);

            if (!$sharedLink->verifyPassword($request->input('password'))) {
                return back()->withErrors(['password' => 'Incorrect password.']);
            }
        }

        $job = $sharedLink->watermarkJob;

        if (!$job->isDone() || !$job->outputExists()) {
            return back()->withErrors(['download' => 'File is not available.']);
        }

        // Record access on the shared link
        $sharedLink->recordAccess($request->ip());

        // Track download for fraud detection
        if (config('watermark.security.track_downloads', true)) {
            $this->trackingService->logSharedLinkAccess(
                $job,
                $request,
                $sharedLink->token,
                $sharedLink->recipient_email
            );
        }

        return response()->download(
            $job->getOutputFullPath(),
            $job->getOutputFilename()
        );
    }

    /**
     * List shared links for a job.
     */
    public function listForJob(WatermarkJob $job)
    {
        $this->authorize('view', $job);

        $links = $job->sharedLinks()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($link) => [
                'id' => $link->id,
                'url' => $link->getUrl(),
                'recipient_email' => $link->recipient_email,
                'recipient_name' => $link->recipient_name,
                'expires_at' => $link->expires_at->format('M j, Y'),
                'is_valid' => $link->isValid(),
                'download_count' => $link->download_count,
                'max_downloads' => $link->max_downloads,
                'has_password' => $link->hasPassword(),
                'created_at' => $link->created_at->diffForHumans(),
            ]);

        return response()->json($links);
    }

    /**
     * Revoke a shared link.
     */
    public function destroy(SharedLink $sharedLink)
    {
        $this->authorize('delete', $sharedLink);

        $sharedLink->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link revoked successfully.',
        ]);
    }

    /**
     * List all user's shared links.
     */
    public function index()
    {
        $links = Auth::user()
            ->sharedLinks()
            ->with('watermarkJob:id,original_filename')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('share.index', compact('links'));
    }
}
