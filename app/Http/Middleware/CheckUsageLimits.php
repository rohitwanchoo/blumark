<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsageLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Only check on job creation routes
        if ($request->routeIs('jobs.store')) {
            $check = $user->canCreateJob();

            if (!$check['allowed']) {
                return redirect()->back()
                    ->with('error', $check['reason'])
                    ->withInput();
            }

            // Store whether to use credits in request for controller
            $request->merge(['use_credits' => $check['use_credits'] ?? false]);
        }

        return $next($request);
    }
}
