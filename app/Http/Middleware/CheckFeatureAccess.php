<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature not available',
                    'message' => "Your current plan does not include access to {$feature}. Please upgrade your plan.",
                ], 403);
            }

            return redirect()->route('billing.plans')
                ->with('error', "Your current plan does not include access to {$feature}. Please upgrade your plan.");
        }

        return $next($request);
    }
}
