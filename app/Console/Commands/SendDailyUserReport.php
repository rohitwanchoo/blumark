<?php

namespace App\Console\Commands;

use App\Mail\DailyUserReportMail;
use App\Models\User;
use App\Models\WatermarkJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyUserReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily user activity report to super admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $superAdminEmail = config('app.super_admin_email');

        if (!$superAdminEmail) {
            $this->error('Super admin email not configured in config/app.php');
            return Command::FAILURE;
        }

        $this->info('Generating daily user activity report...');

        // Get today's date range
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Get this month's date range
        $monthStart = now()->startOfMonth();

        // Calculate revenue from credit purchases today
        $creditRevenueToday = \DB::table('credit_transactions')
            ->join('credit_packs', function($join) {
                $join->whereRaw('FIND_IN_SET(credit_packs.name, credit_transactions.description) > 0');
            })
            ->whereBetween('credit_transactions.created_at', [$today, $todayEnd])
            ->where('credit_transactions.type', 'purchase')
            ->whereNotNull('credit_transactions.stripe_payment_intent_id')
            ->sum('credit_packs.price_cents');

        // Calculate revenue from credit purchases this month
        $creditRevenueMonth = \DB::table('credit_transactions')
            ->join('credit_packs', function($join) {
                $join->whereRaw('FIND_IN_SET(credit_packs.name, credit_transactions.description) > 0');
            })
            ->where('credit_transactions.created_at', '>=', $monthStart)
            ->where('credit_transactions.type', 'purchase')
            ->whereNotNull('credit_transactions.stripe_payment_intent_id')
            ->sum('credit_packs.price_cents');

        // Get new subscriptions today
        $newSubscriptionsToday = \DB::table('subscriptions')
            ->whereBetween('created_at', [$today, $todayEnd])
            ->where('stripe_status', 'active')
            ->count();

        // Calculate revenue from new subscriptions today
        $subscriptionRevenueToday = \DB::table('subscriptions')
            ->join('plans', 'subscriptions.stripe_price', '=', 'plans.stripe_price_id')
            ->whereBetween('subscriptions.created_at', [$today, $todayEnd])
            ->where('subscriptions.stripe_status', 'active')
            ->sum('plans.price_cents');

        // Calculate revenue from new subscriptions this month
        $subscriptionRevenueMonth = \DB::table('subscriptions')
            ->join('plans', 'subscriptions.stripe_price', '=', 'plans.stripe_price_id')
            ->where('subscriptions.created_at', '>=', $monthStart)
            ->where('subscriptions.stripe_status', 'active')
            ->sum('plans.price_cents');

        // Calculate Monthly Recurring Revenue (MRR) from all active subscriptions
        $mrr = \DB::table('subscriptions')
            ->join('plans', 'subscriptions.stripe_price', '=', 'plans.stripe_price_id')
            ->where('subscriptions.stripe_status', 'active')
            ->sum('plans.price_cents');

        // Total revenue today
        $totalRevenueToday = $creditRevenueToday + $subscriptionRevenueToday;

        // Total revenue this month
        $totalRevenueMonth = $creditRevenueMonth + $subscriptionRevenueMonth;

        // Lender submission metrics
        $lenderSubmissionsToday = \App\Models\LenderDistribution::whereBetween('created_at', [$today, $todayEnd])->count();
        $totalLendersSentToday = \App\Models\LenderDistribution::whereBetween('created_at', [$today, $todayEnd])->sum('total_lenders');

        // Collect report data
        $reportData = [
            // New users registered today
            'newUsersToday' => User::whereBetween('created_at', [$today, $todayEnd])->count(),

            // Total users
            'totalUsers' => User::count(),

            // Active users today (users who logged in or created jobs)
            'activeUsersToday' => User::where('updated_at', '>=', $today)
                ->orWhereHas('watermarkJobs', function ($query) use ($today) {
                    $query->where('created_at', '>=', $today);
                })
                ->distinct()
                ->count(),

            // Watermark jobs created today
            'watermarkJobsToday' => WatermarkJob::whereBetween('created_at', [$today, $todayEnd])->count(),

            // Lender submissions
            'lenderSubmissionsToday' => $lenderSubmissionsToday,
            'totalLendersSentToday' => $totalLendersSentToday,

            // Revenue metrics
            'revenueToday' => $totalRevenueToday,
            'revenueMonth' => $totalRevenueMonth,
            'creditRevenueToday' => $creditRevenueToday,
            'subscriptionRevenueToday' => $subscriptionRevenueToday,
            'newSubscriptionsToday' => $newSubscriptionsToday,
            'mrr' => $mrr,

            // Recent users (last 5 registered today)
            'recentUsers' => User::whereBetween('created_at', [$today, $todayEnd])
                ->with('socialAccounts')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        // Send the admin report
        Mail::to($superAdminEmail)->send(new DailyUserReportMail($reportData));

        $this->info("Admin report sent successfully to {$superAdminEmail}");
        $this->table(
            ['Metric', 'Value'],
            [
                ['New Users Today', $reportData['newUsersToday']],
                ['Total Users', $reportData['totalUsers']],
                ['Active Users Today', $reportData['activeUsersToday']],
                ['Watermark Jobs Today', $reportData['watermarkJobsToday']],
                ['Lender Submissions Today', $reportData['lenderSubmissionsToday']],
                ['Total Lenders Sent To', $reportData['totalLendersSentToday']],
                ['Revenue Today', '$' . number_format($totalRevenueToday / 100, 2)],
                ['Revenue This Month', '$' . number_format($totalRevenueMonth / 100, 2)],
                ['MRR', '$' . number_format($mrr / 100, 2)],
            ]
        );

        // Send individual reports to all verified users
        $this->info('Sending individual reports to users...');

        $users = User::whereNotNull('email_verified_at')->get();
        $userCount = 0;

        foreach ($users as $user) {
            // Get user-specific data
            $userToday = now()->startOfDay();
            $userTodayEnd = now()->endOfDay();

            $userData = [
                'planName' => $user->getCurrentPlan()?->name ?? 'Free',
                'creditsRemaining' => $user->getCredits(),
                'monthlyJobCount' => $user->getMonthlyJobCount(),
                'monthlyJobLimit' => $user->getCurrentPlan()?->jobs_limit,
                'jobsRemaining' => $user->getRemainingJobs(),
                'jobsToday' => $user->watermarkJobs()
                    ->whereBetween('created_at', [$userToday, $userTodayEnd])
                    ->count(),
                'recentJobs' => $user->watermarkJobs()
                    ->whereBetween('created_at', [$userToday, $userTodayEnd])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'lenderSubmissionsToday' => $user->lenderDistributions()
                    ->whereBetween('created_at', [$userToday, $userTodayEnd])
                    ->count(),
                'lenderSubmissionsMonth' => $user->lenderDistributions()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'totalLendersSentToday' => $user->lenderDistributions()
                    ->whereBetween('created_at', [$userToday, $userTodayEnd])
                    ->sum('total_lenders'),
            ];

            // Send individual report
            Mail::to($user->email)->send(new \App\Mail\UserDailyReportMail($user, $userData));
            $userCount++;
        }

        $this->info("User reports sent to {$userCount} users");

        return Command::SUCCESS;
    }
}
