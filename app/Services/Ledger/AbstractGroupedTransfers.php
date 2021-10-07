<?php

namespace App\Services\Ledger;

use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Finance\Ledger;
use App\Models\Finance\Transfers;

abstract class AbstractGroupedTransfers extends AbstractLedgerCreate
{
    /**
     * Process Ledger
     *
     * @return Ledger|Collection
     */
    public function process()
    {
        $transfers = $this->transfers->get()
            ->groupBy('party_id');
        $ledgers = new Collection();
        foreach ($transfers as $partyId => $transfer) {
            $party = Party::findOrFail($partyId);
            $this->amount = $transfer->sum('amount');
            $ledger = $this->store($party, 'payable');
            $transferIds = $transfer->pluck('id')->toArray();
            Transfers::whereIn('id', $transferIds)->update(['ledger_id' => $ledger->id]);
            $ledgers->push($ledger);
        }
        return $ledgers;
    }
}
