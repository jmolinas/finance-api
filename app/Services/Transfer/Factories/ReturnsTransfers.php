<?php

namespace App\Services\Transfer\Factories;

use App\Models\Finance\Wallet;

class ReturnsTransfers extends AbstractTransfers
{
    protected static $type = 'return';

    /**
     * Get Wallet from
     *
     * @return Wallet
     */
    protected function getWalletFrom(): Wallet
    {
        return Wallet::typeOf('sale')
            ->wherePartyId($this->partyId)
            ->firstOrfail();
    }

    /**
     * Get Wallet to
     *
     * @return Wallet
     */
    protected function getWalletTo(): Wallet
    {
        return Wallet::typeOf('collection')
            ->wherePartyId($this->partyId)
            ->firstOrfail();
    }
}
