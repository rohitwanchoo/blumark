<?php

namespace App\Http\Controllers;

use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmtpSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Auth::user()->smtpSettings()->latest()->get();

        return view('smtp-settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('smtp-settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'provider_type' => 'nullable|in:smtp,oauth,api_key',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        // If encryption is 'none', set it to null
        if ($validated['encryption'] === 'none') {
            $validated['encryption'] = null;
        }

        // If this is being set as active, deactivate all other settings
        if ($request->boolean('is_active')) {
            Auth::user()->smtpSettings()->update(['is_active' => false]);
        }

        $setting = Auth::user()->smtpSettings()->create($validated);

        return redirect()
            ->route('smtp-settings.index')
            ->with('success', 'SMTP settings created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SmtpSetting $smtpSetting)
    {
        // Ensure user can only edit their own settings
        if ($smtpSetting->user_id !== Auth::id()) {
            abort(403);
        }

        return view('smtp-settings.edit', compact('smtpSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SmtpSetting $smtpSetting)
    {
        // Ensure user can only update their own settings
        if ($smtpSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'provider_type' => 'nullable|in:smtp,oauth,api_key',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        // If encryption is 'none', set it to null
        if ($validated['encryption'] === 'none') {
            $validated['encryption'] = null;
        }

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // If this is being set as active, deactivate all other settings
        if ($request->boolean('is_active')) {
            Auth::user()->smtpSettings()->where('id', '!=', $smtpSetting->id)->update(['is_active' => false]);
        }

        $smtpSetting->update($validated);

        return redirect()
            ->route('smtp-settings.index')
            ->with('success', 'SMTP settings updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SmtpSetting $smtpSetting)
    {
        // Ensure user can only delete their own settings
        if ($smtpSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $smtpSetting->delete();

        return redirect()
            ->route('smtp-settings.index')
            ->with('success', 'SMTP settings deleted successfully.');
    }

    /**
     * Test the SMTP connection
     */
    public function test(SmtpSetting $smtpSetting)
    {
        // Ensure user can only test their own settings
        if ($smtpSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $result = $smtpSetting->testConnection();

        return response()->json($result);
    }

    /**
     * Set as active SMTP setting
     */
    public function activate(SmtpSetting $smtpSetting)
    {
        // Ensure user can only activate their own settings
        if ($smtpSetting->user_id !== Auth::id()) {
            abort(403);
        }

        // Deactivate all other settings
        Auth::user()->smtpSettings()->update(['is_active' => false]);

        // Activate this setting
        $smtpSetting->update(['is_active' => true]);

        return redirect()
            ->route('smtp-settings.index')
            ->with('success', 'SMTP settings activated successfully.');
    }
}
