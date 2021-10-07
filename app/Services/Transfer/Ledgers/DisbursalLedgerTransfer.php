<?php

namespace App\Services\Transfer\Ledgers;

class DisbursalLedgerTransfer extends AbstractLedgerTransfer
{
    protected static $transferType = 'disbursement';

    protected static $toWalletType = 'disbursal';
}
