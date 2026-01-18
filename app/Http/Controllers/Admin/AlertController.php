<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\DocumentAccessLog;
use App\Services\AlertService;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    use LogsAdminActivity;

    public function __construct(
        protected AlertService $alertService
    ) {}

    /**
     * List all alerts with filtering.
     */
    public function index(Request $request)
    {
        $query = Alert::with(['watermarkJob', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($severity = $request->input('severity')) {
            $query->where('severity', $severity);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $alerts = $query->paginate(25)->withQueryString();

        // Get statistics
        $stats = $this->alertService->getStats();

        return view('admin.alerts.index', compact('alerts', 'stats'));
    }

    /**
     * Show alert details.
     */
    public function show(Alert $alert)
    {
        $alert->load(['watermarkJob.user', 'acknowledgedByUser', 'resolvedByUser']);

        // Get related access logs
        $relatedLogs = collect();
        if ($alert->ip_address) {
            $relatedLogs = DocumentAccessLog::where('ip_address', $alert->ip_address)
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        } elseif ($alert->watermark_job_id) {
            $relatedLogs = DocumentAccessLog::where('watermark_job_id', $alert->watermark_job_id)
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        }

        return view('admin.alerts.show', compact('alert', 'relatedLogs'));
    }

    /**
     * Acknowledge an alert.
     */
    public function acknowledge(Alert $alert)
    {
        if (!$alert->isActionable()) {
            return back()->with('error', 'This alert cannot be acknowledged.');
        }

        $this->alertService->acknowledge($alert, auth()->user());
        $this->logActivity('alert.acknowledge', "Acknowledged security alert #{$alert->id}: {$alert->title}", $alert);

        return back()->with('success', 'Alert acknowledged.');
    }

    /**
     * Resolve an alert.
     */
    public function resolve(Request $request, Alert $alert)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!$alert->isActionable()) {
            return back()->with('error', 'This alert cannot be resolved.');
        }

        $this->alertService->resolve($alert, auth()->user(), $request->input('notes'));
        $this->logActivity('alert.resolve', "Resolved security alert #{$alert->id}: {$alert->title}", $alert);

        return back()->with('success', 'Alert resolved.');
    }

    /**
     * Dismiss an alert (false positive).
     */
    public function dismiss(Alert $alert)
    {
        if (!$alert->isActionable()) {
            return back()->with('error', 'This alert cannot be dismissed.');
        }

        $this->alertService->dismiss($alert, auth()->user());
        $this->logActivity('alert.dismiss', "Dismissed security alert #{$alert->id} as false positive", $alert);

        return back()->with('success', 'Alert dismissed as false positive.');
    }
}
