<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function watermarkJobs(): HasMany
    {
        return $this->hasMany(WatermarkJob::class);
    }

    public function userCredits(): HasOne
    {
        return $this->hasOne(UserCredit::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    public function getCredits(): int
    {
        return $this->userCredits?->credits ?? 0;
    }

    public function addCredits(int $amount, string $type, string $description, ?string $stripePaymentIntentId = null): void
    {
        $userCredits = $this->userCredits ?? $this->userCredits()->create(['credits' => 0]);
        $newBalance = $userCredits->credits + $amount;

        $userCredits->update(['credits' => $newBalance]);

        $this->creditTransactions()->create([
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'stripe_payment_intent_id' => $stripePaymentIntentId,
            'balance_after' => $newBalance,
        ]);
    }

    public function useCredits(int $amount, string $description): bool
    {
        $userCredits = $this->userCredits;

        if (!$userCredits || $userCredits->credits < $amount) {
            return false;
        }

        $newBalance = $userCredits->credits - $amount;
        $userCredits->update(['credits' => $newBalance]);

        $this->creditTransactions()->create([
            'amount' => -$amount,
            'type' => 'usage',
            'description' => $description,
            'balance_after' => $newBalance,
        ]);

        return true;
    }

    public function getCurrentPlan(): ?Plan
    {
        $subscription = $this->subscription('default');

        if (!$subscription || !$subscription->active()) {
            return Plan::where('slug', 'free')->first();
        }

        return Plan::where('stripe_price_id', $subscription->stripe_price)->first()
            ?? Plan::where('slug', 'free')->first();
    }

    public function getPlanSlug(): string
    {
        return $this->getCurrentPlan()?->slug ?? 'free';
    }

    public function isOnFreePlan(): bool
    {
        return $this->getPlanSlug() === 'free';
    }

    public function getMonthlyJobCount(): int
    {
        // Count from watermark_jobs table directly for accurate tracking
        return $this->watermarkJobs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function canCreateJob(int $pageCount = 0): array
    {
        $plan = $this->getCurrentPlan();

        if (!$plan) {
            return ['allowed' => false, 'reason' => 'No plan found. Please contact support.'];
        }

        // Check job limit (if not unlimited)
        if ($plan->jobs_limit !== null) {
            $monthlyUsage = $this->getMonthlyJobCount();
            if ($monthlyUsage >= $plan->jobs_limit) {
                // Check if user has credits
                if ($this->getCredits() < 1) {
                    return [
                        'allowed' => false,
                        'reason' => 'Monthly job limit reached. Purchase credits or upgrade your plan.',
                        'monthly_usage' => $monthlyUsage,
                        'monthly_limit' => $plan->jobs_limit,
                    ];
                }
                return ['allowed' => true, 'use_credits' => true, 'credits_required' => 1];
            }
        }

        // Check page limit (if not unlimited)
        if ($plan->pages_per_job_limit !== null && $pageCount > 0 && $pageCount > $plan->pages_per_job_limit) {
            return [
                'allowed' => false,
                'reason' => "PDF exceeds your plan's page limit of {$plan->pages_per_job_limit} pages per job. Please upgrade your plan.",
                'page_limit' => $plan->pages_per_job_limit,
            ];
        }

        return ['allowed' => true, 'use_credits' => false];
    }

    public function getRemainingJobs(): ?int
    {
        $plan = $this->getCurrentPlan();

        if (!$plan || $plan->jobs_limit === null) {
            return null; // Unlimited
        }

        return max(0, $plan->jobs_limit - $this->getMonthlyJobCount());
    }
}
