<?php

namespace App\Http\Controllers;

use App\Models\CreditPack;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function index(Request $request): View
    {
        return view('billing.credits', [
            'creditPacks' => CreditPack::active()->get(),
            'userCredits' => $request->user()->getCredits(),
            'recentTransactions' => $request->user()->creditTransactions()
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get(),
        ]);
    }

    public function purchase(Request $request, CreditPack $creditPack)
    {
        $user = $request->user();

        if (!$creditPack->stripe_price_id) {
            return redirect()->route('billing.credits')
                ->with('error', 'This credit pack is not available for purchase at this time.');
        }

        return $user->checkout([$creditPack->stripe_price_id => 1], [
            'success_url' => route('billing.credits.success') . '?session_id={CHECKOUT_SESSION_ID}&pack=' . $creditPack->id,
            'cancel_url' => route('billing.credits'),
            'metadata' => [
                'credit_pack_id' => $creditPack->id,
                'credits' => $creditPack->getTotalCredits(),
            ],
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $packId = $request->query('pack');

        if ($sessionId && $packId) {
            try {
                $stripe = new \Stripe\StripeClient(config('cashier.secret'));
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    $user = $request->user();
                    $creditPack = CreditPack::find($packId);
                    $paymentIntentId = $session->payment_intent;

                    // Check if credits already added for this payment (avoid duplicates)
                    $alreadyAdded = $user->creditTransactions()
                        ->where('stripe_payment_intent_id', $paymentIntentId)
                        ->exists();

                    if (!$alreadyAdded && $creditPack) {
                        $user->addCredits(
                            $creditPack->getTotalCredits(),
                            'purchase',
                            "Purchased {$creditPack->name} ({$creditPack->getTotalCredits()} credits)",
                            $paymentIntentId
                        );

                        return redirect()->route('billing.credits')
                            ->with('success', "Credits purchased successfully! {$creditPack->getTotalCredits()} credits have been added to your account.");
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Credit purchase success error: ' . $e->getMessage());
            }
        }

        return redirect()->route('billing.credits')
            ->with('success', 'Credits purchased successfully! They will be added to your account shortly.');
    }
}
