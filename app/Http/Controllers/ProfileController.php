<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_type' => ['nullable', 'string', Rule::in(['iso', 'funder'])],
            'phone' => ['nullable', 'string', 'regex:/^\+1 \(\d{3}\) \d{3}-\d{4}$/'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'phone.regex' => 'Phone number must be in format +1 (XXX) XXX-XXXX',
        ]);

        // Update the name field as combination of first and last name
        if ($validated['first_name'] || $validated['last_name']) {
            $validated['name'] = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
