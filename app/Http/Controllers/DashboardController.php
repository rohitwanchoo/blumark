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

        $stats = [
            'total_jobs' => $user->watermarkJobs()->count(),
            'completed_jobs' => $user->watermarkJobs()->where('status', 'done')->count(),
            'pending_jobs' => $user->watermarkJobs()->whereIn('status', ['pending', 'processing'])->count(),
            'failed_jobs' => $user->watermarkJobs()->where('status', 'failed')->count(),
        ];

        $presets = config('watermark.presets', []);
        $defaults = config('watermark.defaults', []);

        return view('dashboard', compact('recentJobs', 'stats', 'presets', 'defaults'));
    }
}
