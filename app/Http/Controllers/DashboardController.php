<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $recentJobs = $user->watermarkJobs()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentDistributions = $user->lenderDistributions()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total_jobs' => $user->watermarkJobs()->count(),
            'completed_jobs' => $user->watermarkJobs()->where('status', 'done')->count(),
            'pending_jobs' => $user->watermarkJobs()->whereIn('status', ['pending', 'processing'])->count(),
            'failed_jobs' => $user->watermarkJobs()->where('status', 'failed')->count(),
        ];

        // Billing & Usage data
        $plan = $user->getCurrentPlan();
        $jobsRemaining = $user->getRemainingJobs();
        $credits = $user->getCredits();
        $billing = [
            'plan_name' => $plan?->name ?? 'Free',
            'plan_slug' => $plan?->slug ?? 'free',
            'jobs_used' => $user->getMonthlyJobCount(),
            'jobs_limit' => $plan?->jobs_limit,
            'jobs_remaining' => $jobsRemaining,
            'credits' => $credits,
            'total_available' => ($jobsRemaining ?? 0) + $credits,
            'is_subscribed' => $user->subscribed('default'),
            'on_grace_period' => $user->subscription('default')?->onGracePeriod() ?? false,
        ];

        // Calculate usage percentage
        if ($billing['jobs_limit'] !== null && $billing['jobs_limit'] > 0) {
            $billing['usage_percentage'] = min(100, round(($billing['jobs_used'] / $billing['jobs_limit']) * 100));
        } else {
            $billing['usage_percentage'] = 0; // Unlimited
        }

        $presets = config('watermark.presets', []);
        $defaults = config('watermark.defaults', []);

        return view('dashboard', compact('recentJobs', 'recentDistributions', 'stats', 'billing', 'presets', 'defaults', 'user'));
    }
}
