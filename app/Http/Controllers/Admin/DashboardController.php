<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Models\WatermarkJob;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_jobs' => WatermarkJob::count(),
            'total_credits_purchased' => CreditTransaction::where('type', 'purchase')->sum('amount'),
            'total_revenue' => CreditTransaction::where('type', 'purchase')
                ->whereNotNull('stripe_payment_intent_id')
                ->count() * 10, // Approximate revenue
        ];

        $jobsByStatus = [
            'pending' => WatermarkJob::where('status', 'pending')->count(),
            'processing' => WatermarkJob::where('status', 'processing')->count(),
            'done' => WatermarkJob::where('status', 'done')->count(),
            'failed' => WatermarkJob::where('status', 'failed')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentJobs = WatermarkJob::with('user')->latest()->take(10)->get();
        $recentActivity = AdminActivityLog::with('admin')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'jobsByStatus', 'recentUsers', 'recentJobs', 'recentActivity'));
    }

    public function activity(Request $request)
    {
        $query = AdminActivityLog::with('admin');

        // Filter by admin
        if ($request->filled('admin')) {
            $query->where('admin_id', $request->admin);
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        // Get admins for filter dropdown
        $admins = User::whereIn('role', ['admin', 'super_admin'])->orderBy('name')->get();

        // Get action types for filter dropdown
        $actionTypes = [
            AdminActivityLog::ACTION_USER_VIEW => 'Viewed User',
            AdminActivityLog::ACTION_USER_UPDATE => 'Updated User',
            AdminActivityLog::ACTION_USER_DELETE => 'Deleted User',
            AdminActivityLog::ACTION_USER_CREDITS => 'Modified Credits',
            AdminActivityLog::ACTION_USER_ROLE => 'Changed Role',
            AdminActivityLog::ACTION_JOB_VIEW => 'Viewed Job',
            AdminActivityLog::ACTION_JOB_DELETE => 'Deleted Job',
            AdminActivityLog::ACTION_IMPERSONATION_START => 'Started Impersonation',
            AdminActivityLog::ACTION_IMPERSONATION_STOP => 'Stopped Impersonation',
        ];

        return view('admin.activity', compact('logs', 'admins', 'actionTypes'));
    }
}
