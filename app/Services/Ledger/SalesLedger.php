<?php

namespace App\Services\Ledger;

use App\Models\Party;
use App\Models\Finance\Ledger;

class SalesLedger extends AbstractLedgerCreate
{
    protected static $type = 'sale';

    /**
     * Process Ledger
     *
     * @return Ledger|Collection
     */
    public function process()
    {
        $partyId = config('settings.burgerprints_party_id');
        $party = Party::findOrFail($partyId);
        $transfers = $this->transfers->builder();
        $this->amount = $transfers->get()->sum('amount');
        $this->store($party, 'receivable');
        $transfers->update(['ledger_id' => $this->ledger->id]);
        return $this->ledger;
    }
}