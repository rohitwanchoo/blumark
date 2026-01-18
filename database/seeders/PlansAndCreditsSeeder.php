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
            'name' => 'Pro',
            'slug' => 'pro',
            'stripe_price_id' => env('STRIPE_PRO_PRICE_ID'),
            'price_cents' => 4999,
            'billing_period' => 'monthly',
            'jobs_limit' => 300,
            'pages_per_job_limit' => null,
            'features' => [
                '300 jobs per month',
                'Unlimited pages per job',
                'All watermark positions',
                'Priority processing',
                'Batch uploads',
                'API access',
                'Email support',
            ],
            'sort_order' => 2,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'stripe_price_id' => env('STRIPE_ENTERPRISE_PRICE_ID'),
            'price_cents' => 9999,
            'billing_period' => 'monthly',
            'jobs_limit' => null,
            'pages_per_job_limit' => null,
            'features' => [
                'Unlimited jobs',
                'Unlimited pages per job',
                'All watermark positions',
                'Priority processing',
                'Batch uploads',
                'API access',
                'Priority support',
                'Custom watermark templates',
            ],
            'sort_order' => 3,
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
