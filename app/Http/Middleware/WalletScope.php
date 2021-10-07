<?php

namespace App\Http\Middleware;

use App\Models\Finance\Wallet;
use Closure;

class WalletScope
{
    /**
     * Handle incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * 
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        Wallet::addGlobalScope(
            'wallet', 
            function ($builder) use ($user) {
                $builder->wherePartyId($user->party_id);
            }
        );

        return $next($request);
    }
}
