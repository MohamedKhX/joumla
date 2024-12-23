<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckWholesaleStoreSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->wholesaleStore) {
            $activeSubscription = $user->wholesaleStore->subscriptions()
                ->where('end_date', '>=', now())
                ->where('status', 'active')
                ->exists();

            if (!$activeSubscription && !$request->is('*/subscription-expired*')) {
                return redirect('/wholesaleStore/subscription-expired');
            }
        }

        return $next($request);
    }
} 