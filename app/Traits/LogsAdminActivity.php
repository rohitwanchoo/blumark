<?php

namespace App\Traits;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsAdminActivity
{
    /**
     * Log an admin activity.
     */
    protected function logActivity(
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $properties = null
    ): AdminActivityLog {
        // Get the actual admin ID (in case of impersonation, get the impersonator)
        $adminId = session('impersonator_id') ?? Auth::id();

        return AdminActivityLog::create([
            'admin_id' => $adminId,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a user-related activity.
     */
    protected function logUserActivity(string $action, string $description, Model $user, ?array $properties = null): AdminActivityLog
    {
        return $this->logActivity($action, $description, $user, $properties);
    }

    /**
     * Log a job-related activity.
     */
    protected function logJobActivity(string $action, string $description, Model $job, ?array $properties = null): AdminActivityLog
    {
        return $this->logActivity($action, $description, $job, $properties);
    }
}
