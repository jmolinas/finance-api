<?php

namespace App\Services\Transfer\Ledgers;

use App\Models\Finance\Wallet;
use App\Services\Transfer\TransferService;
use App\Models\Finance\Transfers;
use App\Models\Finance\Ledger;
use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

abstract class AbstractLedgerTransfer
{
    protected $transferService;

    protected static $transferType;

    protected static $status = 'settled';

    protected static $toWalletType;

    protected $toPartyId;

    /**
     * Ledger Transfers
     *
     * @param TransferService $transferService
     */
    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Get Wallet
     *
     * @param string $type
     * @param integer $partyId
     * 
     * @return Wallet
     */
    protected function getWallet($type, int $partyId): Wallet
    {
        return Wallet::where(['type' => $type, 'party_id' => $partyId])
            ->firstOrFail();
    }

    /**
     * Get Wallet From
     * 
     * @return Wallet
     */
    public function getFromWallet(): Wallet
    {
        $partyId = config('settings.burgerprints_party_id');
        return $this->getWallet('collection', $partyId);
    }

    /**
     * Get Wallet To
     * 
     * @param integer $partyId
     * 
     * @return Wallet
     */
    protected function getToWallet(int $partyId): Wallet
    {
        if (static::$toWalletType === null) {
            throw new \Exception('Implementing class static::$toWalletType required');
        }
        return $this->getWallet(static::$toWalletType, $partyId);
    }

    /**
     * Transfer Ledger
     *
     * @param Ledger $ledger
     * 
     * @return Transfers
     */
    public function transfer(Ledger $ledger)
    {
        if (static::$transferType === null) {
            throw new \Exception('Implementing class static::$transferType required');
        }

        $party = Party::findOrFail($ledger->party_id);

        $transfer = $this->transferService
            ->setParty($party)
            ->setWalletFrom($this->getFromWallet())
            ->setWalletTo($this->getToWallet($ledger->party_id))
            ->setAmount($ledger->amount)
            ->create(
                static::$transferType,
                $ledger->id,
                "Transfer for Ledger#{$ledger->id} amounting {$ledger->amount}"
            );

        $ledger->transfer_id = $transfer->id;
        $ledger->status = static::$status;

        if (static::$status === 'settled') {
            $ledger->settled_at = Date::now();
        }
        $ledger->save();
        return $transfer;
    }

    /**
     * Transfer Ledger Collection
     *
     * @param Collection $collection
     * 
     * @return Collection
     */
    public function transferCollection(Collection $collection)
    {
        $collection = $collection->groupBy('party_id');
        $transfers = new Collection();
        foreach ($collection as $ledger) {
            $ledger->each(
                function ($item) use ($transfers) {
                    $transfer = $this->transfer($item);
                    $transfers->push($transfer);
                }
            );
        }
        return $transfers;
    }
}
