<?php

namespace App\Http\Controllers;

use App\Factories\TransactionMatcher\ShippingBillsMatcher;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Match Shipping Billing
     *
     * @param Request $request
     * 
     * @return void
     */
    public function matchShippingBilling(Request $request)
    {
        $this->validate(
            $request,
            [
                'billing' => 'required|mimetypes:text/csv'
            ]
        );

        $matcher = new ShippingBillsMatcher($request->file('billing'));
        $matcher->process();
    }
}
