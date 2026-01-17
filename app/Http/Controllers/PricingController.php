<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\CreditPack;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        return view('pricing', [
            'plans' => Plan::active()->get(),
            'creditPacks' => CreditPack::active()->get(),
        ]);
    }
}
