<?php

namespace App\Http\Controllers;

use App\Mail\PaymentNotificationMail;
use App\Models\CreditPack;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends CashierController
{
    /**
     * Handle checkout session completed (for credit purchases).
     */
    public function handleCheckoutSessionCompleted(array $payload): Response
    {
        $session = $payload['data']['object'];

        // Check if this is a credit purchase
        if (isset($session['metadata']['credit_pack_id'])) {
            $this->handleCreditPurchase($session);
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment succeeded (for subscription payments).
     */
    public function handleInvoicePaymentSucceeded(array $payload): Response
    {
        $invoice = $payload['data']['object'];

        // Get the customer
        $customerId = $invoice['customer'] ?? null;
        if (!$customerId) {
            return $this->successMethod();
        }

        $user = User::where('stripe_id', $customerId)->first();
        if (!$user) {
            Log::warning('User not found for subscription payment', ['stripe_id' => $customerId]);
            return $this->successMethod();
        }

        // Get subscription details
        $subscription = $user->subscription('default');
        if (!$subscription) {
            return $this->successMethod();
        }

        $plan = $user->getCurrentPlan();
        $amount = $invoice['amount_paid'] ?? 0;

        // Send payment notification to super admin
        $superAdminEmail = config('app.super_admin_email');
        if ($superAdminEmail && $amount > 0) {
            Mail::to($superAdminEmail)->send(new PaymentNotificationMail(
                $user,
                'subscription',
                [
                    'amount' => $amount,
                    'description' => "{$plan?->name} Plan - Monthly Subscription",
                    'payment_intent_id' => $invoice['payment_intent'] ?? null,
                ]
            ));
        }

        Log::info('Subscription payment notification sent', [
            'user_id' => $user->id,
            'amount' => $amount,
            'plan' => $plan?->name,
        ]);

        return $this->successMethod();
    }

    protected function handleCreditPurchase(array $session): void
    {
        $creditPackId = $session['metadata']['credit_pack_id'] ?? null;

        if (!$creditPackId) {
            Log::warning('Credit purchase webhook received without credit_pack_id', $session);
            return;
        }

        $creditPack = CreditPack::find($creditPackId);

        if (!$creditPack) {
            Log::warning('Credit pack not found for purchase', ['credit_pack_id' => $creditPackId]);
            return;
        }

        $customerId = $session['customer'] ?? null;

        if (!$customerId) {
            Log::warning('No customer ID in checkout session', $session);
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (!$user) {
            Log::warning('User not found for Stripe customer', ['stripe_id' => $customerId]);
            return;
        }

        $paymentIntentId = $session['payment_intent'] ?? null;

        $user->addCredits(
            $creditPack->getTotalCredits(),
            'purchase',
            "Purchased {$creditPack->name} ({$creditPack->getTotalCredits()} credits)",
            $paymentIntentId
        );

        Log::info('Credits added to user account', [
            'user_id' => $user->id,
            'credits' => $creditPack->getTotalCredits(),
            'credit_pack' => $creditPack->name,
        ]);

        // Send payment notification to super admin
        $superAdminEmail = config('app.super_admin_email');
        if ($superAdminEmail) {
            Mail::to($superAdminEmail)->send(new PaymentNotificationMail(
                $user,
                'credit_purchase',
                [
                    'amount' => $creditPack->price_cents,
                    'description' => "{$creditPack->name} - {$creditPack->getTotalCredits()} credits",
                    'payment_intent_id' => $paymentIntentId,
                ]
            ));
        }
    }
}
