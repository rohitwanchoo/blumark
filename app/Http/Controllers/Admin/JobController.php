<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\WatermarkJob;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $query = WatermarkJob::with('user');

        if ($request->filled('user')) {
            $search = $request->user;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.jobs.index', compact('jobs'));
    }

    public function show(WatermarkJob $job)
    {
        $job->load('user');

        $this->logJobActivity(
            AdminActivityLog::ACTION_JOB_VIEW,
            "Viewed job #{$job->id}: {$job->original_filename}",
            $job
        );

        return view('admin.jobs.show', compact('job'));
    }

    public function destroy(WatermarkJob $job)
    {
        $jobData = [
            'id' => $job->id,
            'filename' => $job->original_filename,
            'user_id' => $job->user_id,
            'user_name' => $job->user?->name,
            'status' => $job->status,
        ];

        // Log before deletion
        $this->logActivity(
            AdminActivityLog::ACTION_JOB_DELETE,
            "Deleted job #{$job->id}: {$job->original_filename}",
            null,
            ['deleted_job' => $jobData]
        );

        $job->deleteFiles();
        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
