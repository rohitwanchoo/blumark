<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('watermarkJobs')
            ->with('userCredits')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['userCredits', 'watermarkJobs' => function ($q) {
            $q->latest()->take(10);
        }, 'creditTransactions' => function ($q) {
            $q->latest()->take(20);
        }]);

        $stats = [
            'total_jobs' => $user->watermarkJobs()->count(),
            'total_pages' => $user->watermarkJobs()->sum('page_count'),
            'credits' => $user->getCredits(),
        ];

        $this->logUserActivity(
            AdminActivityLog::ACTION_USER_VIEW,
            "Viewed user profile: {$user->name} ({$user->email})",
            $user
        );

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $oldValues = $user->only(array_keys($validated));
        $user->update($validated);

        $this->logUserActivity(
            AdminActivityLog::ACTION_USER_UPDATE,
            "Updated user: {$user->name} ({$user->email})",
            $user,
            ['old' => $oldValues, 'new' => $validated]
        );

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin() && !Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'You cannot delete a super admin.');
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        // Log before deletion
        $this->logActivity(
            AdminActivityLog::ACTION_USER_DELETE,
            "Deleted user: {$user->name} ({$user->email})",
            null,
            ['deleted_user' => $userData]
        );

        // Delete user's jobs and files
        foreach ($user->watermarkJobs as $job) {
            $job->deleteFiles();
            $job->delete();
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function addCredits(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:-1000', 'max:10000'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $oldCredits = $user->getCredits();
        $type = $validated['amount'] >= 0 ? 'admin_grant' : 'admin_deduct';
        $user->addCredits($validated['amount'], $type, $validated['reason']);

        $this->logUserActivity(
            AdminActivityLog::ACTION_USER_CREDITS,
            "Modified credits for {$user->name}: {$validated['amount']} ({$validated['reason']})",
            $user,
            [
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'old_balance' => $oldCredits,
                'new_balance' => $user->fresh()->getCredits(),
            ]
        );

        return back()->with('success', "Credits {$validated['amount']} added successfully.");
    }

    public function updateRole(Request $request, User $user)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Only super admins can change user roles.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin', 'super_admin'])],
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        $this->logUserActivity(
            AdminActivityLog::ACTION_USER_ROLE,
            "Changed role for {$user->name}: {$oldRole} → {$validated['role']}",
            $user,
            ['old_role' => $oldRole, 'new_role' => $validated['role']]
        );

        return back()->with('success', 'User role updated successfully.');
    }
}
