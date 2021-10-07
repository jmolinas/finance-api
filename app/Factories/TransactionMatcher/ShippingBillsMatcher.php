<?php

namespace App\Factories\TransactionMatcher;

use App\Services\Ledger\ShippingLedger;

class ShippingBillsMatcher extends AbstractMatcher
{
    const FILTER = 'order_id';

    const TRANSFER_TYPE = ShippingLedger::class;

    const INDEX = [
        'order_id' => 1,
        'amount' => 2
    ];
}
