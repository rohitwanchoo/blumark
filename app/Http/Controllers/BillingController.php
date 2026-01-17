<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\CreditPack;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('billing.index', [
            'user' => $user,
            'currentPlan' => $user->getCurrentPlan(),
            'credits' => $user->getCredits(),
            'monthlyUsage' => $user->getMonthlyJobCount(),
            'remainingJobs' => $user->getRemainingJobs(),
            'plans' => Plan::active()->get(),
            'creditPacks' => CreditPack::active()->get(),
            'recentTransactions' => $user->creditTransactions()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ]);
    }

    public function paymentMethods(Request $request): View
    {
        $user = $request->user();

        return view('billing.payment-methods', [
            'intent' => $user->createSetupIntent(),
            'paymentMethods' => $user->paymentMethods(),
            'defaultPaymentMethod' => $user->defaultPaymentMethod(),
        ]);
    }

    public function addPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $user->addPaymentMethod($request->payment_method);

        if (!$user->hasDefaultPaymentMethod()) {
            $user->updateDefaultPaymentMethod($request->payment_method);
        }

        return redirect()->route('billing.payment-methods')
            ->with('success', 'Payment method added successfully.');
    }

    public function removePaymentMethod(Request $request, string $paymentMethodId)
    {
        $user = $request->user();
        $paymentMethod = $user->findPaymentMethod($paymentMethodId);

        if ($paymentMethod) {
            $paymentMethod->delete();
        }

        return redirect()->route('billing.payment-methods')
            ->with('success', 'Payment method removed.');
    }

    public function setDefaultPaymentMethod(Request $request, string $paymentMethodId)
    {
        $user = $request->user();
        $user->updateDefaultPaymentMethod($paymentMethodId);

        return redirect()->route('billing.payment-methods')
            ->with('success', 'Default payment method updated.');
    }

    public function invoices(Request $request): View
    {
        return view('billing.invoices', [
            'invoices' => $request->user()->invoices(),
        ]);
    }

    public function downloadInvoice(Request $request, string $invoiceId)
    {
        return $request->user()->downloadInvoice($invoiceId, [
            'vendor' => 'BluMark',
            'product' => 'PDF Watermarking Service',
        ]);
    }
}
