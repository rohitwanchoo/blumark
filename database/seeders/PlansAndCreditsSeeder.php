<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\CreditPack;
use Illuminate\Database\Seeder;

class PlansAndCreditsSeeder extends Seeder
{
    public function run(): void
    {
        // Create plans
        Plan::create([
            'name' => 'Free',
            'slug' => 'free',
            'stripe_price_id' => null,
            'price_cents' => 0,
            'billing_period' => 'monthly',
            'jobs_limit' => 5,
            'pages_per_job_limit' => 10,
            'features' => [
                '5 jobs per month',
                '10 pages max per job',
                'Basic watermark positions',
                'Standard processing',
            ],
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'stripe_price_id' => env('STRIPE_BASIC_PRICE_ID'),
            'price_cents' => 900,
            'billing_period' => 'monthly',
            'jobs_limit' => 50,
            'pages_per_job_limit' => 100,
            'features' => [
                '50 jobs per month',
                '100 pages max per job',
                'All watermark positions',
                'Priority processing',
                'Email support',
            ],
            'sort_order' => 2,
        ]);

        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
            'price_cents' => 2900,
            'billing_period' => 'monthly',
            'jobs_limit' => 200,
            'pages_per_job_limit' => 500,
            'features' => [
                '200 jobs per month',
                '500 pages max per job',
                'All watermark positions',
                'Priority processing',
                'Priority email support',
                'API access',
            ],
            'sort_order' => 3,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'stripe_price_id' => env('STRIPE_ENTERPRISE_PRICE_ID'),
            'price_cents' => 9900,
            'billing_period' => 'monthly',
            'jobs_limit' => null,
            'pages_per_job_limit' => null,
            'features' => [
                'Unlimited jobs',
                'Unlimited pages per job',
                'All watermark positions',
                'Fastest processing',
                'Dedicated support',
                'API access',
                'Custom integrations',
            ],
            'sort_order' => 4,
        ]);

        // Create credit packs
        CreditPack::create([
            'name' => 'Starter Pack',
            'slug' => 'starter',
            'stripe_price_id' => env('STRIPE_CREDIT_STARTER_PRICE_ID'),
            'credits' => 20,
            'price_cents' => 500,
            'bonus_credits' => 0,
            'is_popular' => false,
            'sort_order' => 1,
        ]);

        CreditPack::create([
            'name' => 'Value Pack',
            'slug' => 'value',
            'stripe_price_id' => env('STRIPE_CREDIT_VALUE_PRICE_ID'),
            'credits' => 100,
            'price_cents' => 2000,
            'bonus_credits' => 0,
            'is_popular' => true,
            'sort_order' => 2,
        ]);

        CreditPack::create([
            'name' => 'Bulk Pack',
            'slug' => 'bulk',
            'stripe_price_id' => env('STRIPE_CREDIT_BULK_PRICE_ID'),
            'credits' => 300,
            'price_cents' => 5000,
            'bonus_credits' => 0,
            'is_popular' => false,
            'sort_order' => 3,
        ]);
    }
}
