<?php

namespace App\Services\Transfer\Ledgers;

class ShippingLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $transferType = 'shipping';

    protected static $toWalletType = 'settlement';
}
