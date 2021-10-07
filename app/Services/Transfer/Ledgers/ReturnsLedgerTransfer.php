<?php

namespace App\Services\Transfer\Ledgers;

class ReturnsLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $toWalletType = 'returns';

    protected static $transferType = 'settled';
}
