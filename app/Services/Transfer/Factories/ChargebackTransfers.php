<?php

namespace App\Services\Transfer\Factories;

use App\Models\Finance\Wallet;

class ChargebackTransfers extends AbstractTransfers
{
    protected static $type = 'chargeback';

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
        return Wallet::typeOf('chargeback')
            ->wherePartyId($this->party->id)
            ->firstOrfail();
    }
}
