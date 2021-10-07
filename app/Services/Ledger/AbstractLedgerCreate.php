<?php

namespace App\Services\Ledger;

use App\Models\Finance\Ledger;
use App\Models\Party;
use App\Services\Transfer\TransfersFetcher;

abstract class AbstractLedgerCreate
{
    protected $ledger;

    protected $transfers;

    protected static $type;

    protected static $currency = 'USD';

    protected $amount = 0;

    /**
     * Ledger Create
     *
     * @param TransferService $transfers
     * @param Ledger $ledger
     */
    public function __construct(Ledger $ledger, TransfersFetcher $transfers)
    {
        $this->ledger = $ledger;
        $this->transfers = $transfers;
        $this->transfers->setType(static::$type);
    }

    /**
     * Filter transfers
     *
     * @param array $transferIds
     * @param string|date $from
     * @param string|date $to
     * 
     * @return void
     */
    public function filterTransfers(array $transferIds = [], $from = null, $to = null)
    {
        $this->transfers->setParams($transferIds, $from, $to);
        return $this;
    }

    /**
     * Create Ledger
     *
     * @param Party     $party
     * @param string    $type
     * 
     * @return Ledger
     */
    public function store(Party $party, $type) : Ledger
    {
        if ($this->amount <= 0) {
            throw new \RuntimeException("Amount must be greater than 0");
        }
        $this->ledger = $this->ledger->create(
            [
                'party_id' => $party->id,
                'type' => $type,
                'amount' => $this->amount,
                'status' => 'pending',
                'currency_code' => static::$currency,
                'transaction_type' => static::$type
            ]
        );
        return $this->ledger;
    }
}