<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function plans(Request $request): View
    {
        return view('billing.plans', [
            'plans' => Plan::active()->get(),
            'currentPlan' => $request->user()->getCurrentPlan(),
        ]);
    }

    public function checkout(Request $request, Plan $plan)
    {
        $user = $request->user();
        $currentPlan = $user->getCurrentPlan();

        // If user already has this plan
        if ($currentPlan?->id === $plan->id) {
            return redirect()->route('billing.index')
                ->with('info', 'You are already on this plan.');
        }

        // Free plan - cancel subscription at end of billing period
        if ($plan->slug === 'free') {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancel();
            }
            return redirect()->route('billing.index')
                ->with('success', 'Your subscription will be cancelled at the end of the current billing period. You\'ll retain access until then.');
        }

        // Check if plan has a Stripe price ID
        if (!$plan->stripe_price_id) {
            return redirect()->route('billing.plans')
                ->with('error', 'This plan is not available for subscription at this time.');
        }

        // If user is already subscribed, handle plan change
        if ($user->subscribed('default')) {
            $isDowngrade = $currentPlan && $plan->price_cents < $currentPlan->price_cents;

            if ($isDowngrade) {
                // For downgrades, schedule change at end of billing period
                $subscription = $user->subscription('default');
                $stripe = new \Stripe\StripeClient(config('cashier.secret'));

                $stripe->subscriptions->update($subscription->stripe_id, [
                    'items' => [
                        [
                            'id' => $subscription->items->first()->stripe_id,
                            'price' => $plan->stripe_price_id,
                        ],
                    ],
                    'proration_behavior' => 'none',
                    'billing_cycle_anchor' => 'unchanged',
                ]);

                return redirect()->route('billing.index')
                    ->with('success', 'Your plan will be downgraded to ' . $plan->name . ' at the end of your current billing cycle. You\'ll retain your current plan features until then.');
            }

            // For upgrades, apply immediately with proration
            $user->subscription('default')->swap($plan->stripe_price_id);

            return redirect()->route('billing.index')
                ->with('success', 'Your plan has been upgraded to ' . $plan->name . ' successfully!');
        }

        // New subscription - redirect to Stripe Checkout
        return $user->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('billing.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing.plans'),
            ]);
    }

    public function success(Request $request)
    {
        return redirect()->route('billing.index')
            ->with('success', 'Subscription activated successfully! Thank you for subscribing.');
    }

    public function cancel(Request $request)
    {
        $user = $request->user();

        if ($user->subscribed('default')) {
            $user->subscription('default')->cancel();
        }

        return redirect()->route('billing.index')
            ->with('success', 'Your subscription has been cancelled. It will remain active until the end of the current billing period.');
    }

    public function resume(Request $request)
    {
        $user = $request->user();

        if ($user->subscription('default')?->onGracePeriod()) {
            $user->subscription('default')->resume();
        }

        return redirect()->route('billing.index')
            ->with('success', 'Your subscription has been resumed.');
    }

    public function swap(Request $request, Plan $plan)
    {
        $user = $request->user();

        if (!$user->subscribed('default')) {
            return redirect()->route('billing.subscription.checkout', $plan);
        }

        if (!$plan->stripe_price_id) {
            return redirect()->route('billing.plans')
                ->with('error', 'This plan is not available for subscription.');
        }

        $currentPlan = $user->getCurrentPlan();
        $isDowngrade = $currentPlan && $plan->price_cents < $currentPlan->price_cents;

        if ($isDowngrade) {
            // For downgrades, schedule change at end of billing period
            $subscription = $user->subscription('default');
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));

            $stripe->subscriptions->update($subscription->stripe_id, [
                'items' => [
                    [
                        'id' => $subscription->items->first()->stripe_id,
                        'price' => $plan->stripe_price_id,
                    ],
                ],
                'proration_behavior' => 'none',
                'billing_cycle_anchor' => 'unchanged',
            ]);

            return redirect()->route('billing.index')
                ->with('success', 'Your plan will be downgraded to ' . $plan->name . ' at the start of your next billing cycle.');
        }

        // For upgrades, apply immediately with proration
        $user->subscription('default')->swap($plan->stripe_price_id);

        return redirect()->route('billing.index')
            ->with('success', 'Your plan has been upgraded to ' . $plan->name . ' successfully.');
    }
}
