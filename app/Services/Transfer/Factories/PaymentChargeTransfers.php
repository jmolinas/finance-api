<?php

namespace App\Services\Transfer\Factories;

use App\Models\Finance\Wallet;

class PaymentChargeTransfers extends AbstractTransfers
{
    protected static $type = 'charges';

    /**
     * Get Wallet to
     *
     * @return Wallet
     */
    protected function getWalletTo(): Wallet
    {
        return Wallet::typeOf('charges')
            ->wherePartyId($this->party->id)
            ->firstOrfail();
    }
}
