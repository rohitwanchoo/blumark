<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    use LogsAdminActivity;

    public function impersonate(User $user)
    {
        $admin = Auth::user();

        // Prevent impersonating yourself
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Prevent non-super-admins from impersonating admins
        if ($user->isAdmin() && !$admin->isSuperAdmin()) {
            return back()->with('error', 'Only super admins can impersonate other admins.');
        }

        // Log impersonation start
        $this->logUserActivity(
            AdminActivityLog::ACTION_IMPERSONATION_START,
            "Started impersonating user: {$user->name} ({$user->email})",
            $user,
            ['impersonated_user_role' => $user->role]
        );

        // Store original admin ID in session
        Session::put('impersonating', true);
        Session::put('impersonator_id', $admin->id);
        Session::put('impersonator_name', $admin->name);

        // Login as the target user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    public function stop()
    {
        if (!Session::has('impersonator_id')) {
            return redirect()->route('dashboard')->with('error', 'You are not impersonating anyone.');
        }

        $adminId = Session::get('impersonator_id');
        $impersonatedUser = Auth::user();
        $admin = User::find($adminId);

        if (!$admin) {
            Session::forget(['impersonating', 'impersonator_id', 'impersonator_name']);
            Auth::logout();
            return redirect()->route('login')->with('error', 'Original admin account not found.');
        }

        // Log impersonation stop (use admin ID directly since we're still logged in as impersonated user)
        AdminActivityLog::create([
            'admin_id' => $adminId,
            'action' => AdminActivityLog::ACTION_IMPERSONATION_STOP,
            'description' => "Stopped impersonating user: {$impersonatedUser->name} ({$impersonatedUser->email})",
            'subject_type' => User::class,
            'subject_id' => $impersonatedUser->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Clear impersonation session
        Session::forget(['impersonating', 'impersonator_id', 'impersonator_name']);

        // Login back as admin
        Auth::login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'You have stopped impersonating.');
    }
}
