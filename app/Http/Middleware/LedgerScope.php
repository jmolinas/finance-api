<?php

namespace App\Http\Middleware;

use App\Models\Finance\Ledger;
use Closure;

class LedgerScope
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
        Ledger::addGlobalScope(
            'ledger', 
            function ($builder) use ($user) {
                $builder->wherePartyId($user->party_id);
            }
        );

        return $next($request);
    }
}
