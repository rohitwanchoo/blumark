<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'company_name',
        'company_type',
        'phone',
        'website',
        'address',
        'timezone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trial_ends_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
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

    public function watermarkTemplates(): HasMany
    {
        return $this->hasMany(WatermarkTemplate::class);
    }

    public function sharedLinks(): HasMany
    {
        return $this->hasMany(SharedLink::class);
    }

    public function batchJobs(): HasMany
    {
        return $this->hasMany(BatchJob::class);
    }

    public function lenders(): HasMany
    {
        return $this->hasMany(Lender::class);
    }

    public function lenderDistributions(): HasMany
    {
        return $this->hasMany(LenderDistribution::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function smtpSettings(): HasMany
    {
        return $this->hasMany(SmtpSetting::class);
    }

    public function hasSocialAccount(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    public function getFullName(): string
    {
        if ($this->first_name || $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }
        return $this->name;
    }

    public function getCompanyTypeLabel(): ?string
    {
        return match($this->company_type) {
            'iso' => 'ISO',
            'funder' => 'Funder',
            default => null,
        };
    }

    public function getFormattedPhone(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $this->phone);

        // Handle different formats
        if (str_starts_with($phone, '+1') && strlen($phone) === 12) {
            // +1XXXXXXXXXX format
            $number = substr($phone, 2);
            return '+1 (' . substr($number, 0, 3) . ') ' . substr($number, 3, 3) . '-' . substr($number, 6);
        } elseif (str_starts_with($phone, '1') && strlen($phone) === 11) {
            // 1XXXXXXXXXX format
            $number = substr($phone, 1);
            return '+1 (' . substr($number, 0, 3) . ') ' . substr($number, 3, 3) . '-' . substr($number, 6);
        } elseif (strlen($phone) === 10) {
            // XXXXXXXXXX format (assume US/Canada)
            return '+1 (' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }

        // Return original if format not recognized
        return $this->phone;
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
        // Count jobs for billing: batch uploads count as 1 job, individual uploads count as 1 each
        $thisMonth = now();

        // Count individual jobs (not part of a batch)
        $individualJobs = $this->watermarkJobs()
            ->whereNull('batch_job_id')
            ->whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)
            ->count();

        // Count batch jobs (each batch = 1 job regardless of file count)
        $batchJobs = $this->batchJobs()
            ->whereMonth('created_at', $thisMonth->month)
            ->whereYear('created_at', $thisMonth->year)
            ->count();

        return $individualJobs + $batchJobs;
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

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            default => 'User',
        };
    }

    public function canDisconnectSocialAccount(string $provider): bool
    {
        $hasPassword = $this->password !== null && $this->password !== '';
        $otherSocialAccounts = $this->socialAccounts()->where('provider', '!=', $provider)->count();

        return $hasPassword || $otherSocialAccounts > 0;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
