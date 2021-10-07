<?php

namespace App\Services\Transfer\Ledgers;

class SettlementLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $toWalletType = 'settlement';

    protected static $transferType = 'settlement';
}
