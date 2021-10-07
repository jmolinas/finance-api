<?php

namespace App\Services\Transfer\Ledgers;

class CollectionLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $transferType = 'revenue';

    protected static $toWalletType = 'fund';
}
