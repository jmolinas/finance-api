<?php

namespace App\Services\Transfer\Ledgers;

class SalesLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $transferType = 'revenue';

    protected static $toWalletType = 'sale';
}
