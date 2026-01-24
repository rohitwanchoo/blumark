<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthController extends Controller
{
    protected array $providers = ['google', 'linkedin', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')
                ->with('error', 'Invalid social login provider.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->providers)) {
            return redirect()->route('login')
                ->with('error', 'Invalid social login provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to login with ' . ucfirst($provider) . '. Please try again.');
        }

        // Check if this social account is already linked to a user
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            // Login the existing user
            Auth::login($socialAccount->user);
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back!');
        }

        // Check if a user exists with this email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Link this social account to the existing user
            $this->createSocialAccount($existingUser, $provider, $socialUser);
            Auth::login($existingUser);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Your ' . ucfirst($provider) . ' account has been linked.');
        }

        // Create a new user
        $user = $this->createUser($socialUser);
        $this->createSocialAccount($user, $provider, $socialUser);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to BluMark! Your account has been created.');
    }

    protected function createUser(SocialiteUser $socialUser): User
    {
        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(24)),
            'email_verified_at' => now(),
        ]);
    }

    protected function createSocialAccount(User $user, string $provider, SocialiteUser $socialUser): SocialAccount
    {
        return $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => isset($socialUser->expiresIn)
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }
}
