<?php

namespace App\Services\Transfer;

use App\Models\Finance\Transfers;
use App\Models\Finance\Wallet;
use App\Services\Wallet\WalletLogger;
use App\Models\Party;

class TransferService
{
    protected $fromWallet,
        $toWallet,
        $amount,
        $transfers,
        $party,
        $wallet;

    /**
     * Transfer Service
     *
     * @param Wallet $wallet
     * @param Transfers $transfers
     */
    public function __construct(Wallet $wallet, Transfers $transfers)
    {
        $this->wallet = $wallet;
        $this->transfers = $transfers;
    }

    /**
     * Set Party
     *
     * @param Party $party
     * 
     * @return TransferService
     */
    public function setParty(Party $party)
    {
        $this->party = $party;
        return $this;
    }

    /**
     * Get Party
     *
     * @return Party|null
     */
    public function getParty(): ?Party
    {
        return $this->party;
    }

    /**
     * Build Data
     *
     * @param string $type
     * @param string $transactionId
     * @param string $details
     * @param json $metadata
     * @param string $orderId
     * @param string $campaignId
     * @param string $productId
     * @param string $sku
     * @param string $transactionDate
     * 
     * @return array
     */
    protected function buildData(
        $type,
        $transactionId,
        $details,
        $metadata = null,
        $orderId = null,
        $campaignId = null,
        $productId = null,
        $sku = null,
        $transactionDate = null
    ) {
        $this->verifyWallet();
        $this->verifyAmount($this->amount);
        $partyId = $this->party ? $this->party->id : null;
        return [
            'from_wallet_id' => $this->fromWallet->id,
            'to_wallet_id' => $this->toWallet->id,
            'amount' => $this->amount,
            'type' => $type,
            'party_id' => $partyId,
            'metadata' => $metadata,
            'details' => $details,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'campaign_id' => $campaignId,
            'product_id' => $productId,
            'sku' => $sku,
            'transaction_date' => $transactionDate
        ];
    }

    /**
     * Has Entry
     *
     * @param string $type
     * @param string $transactionId
     * 
     * @return Transfer
     */
    protected function transfer($type, $transactionId) : ?Transfers
    {
        $instance = $this->transfers
            ->where(
                [
                    'type' => $type,
                    'transaction_id' => $transactionId
                ]
            )
            ->first();
        return $instance;
    }

    /**
     * Create transfers
     *
     * @param string $type
     * @param string $transactionId
     * @param string $details
     * @param json $metadata
     * @param string $orderId
     * @param string $campaignId
     * 
     * @return Transfers
     */
    public function create(
        $type,
        $transactionId,
        $details,
        $metadata = null,
        $orderId = null,
        $campaignId = null,
        $productId = null,
        $sku = null,
        $transactionDate = null
    ): Transfers {
        $data = $this->buildData(
            $type,
            $transactionId,
            $details,
            $metadata,
            $orderId,
            $campaignId,
            $productId,
            $sku,
            $transactionDate
        );
        $instance = $this->transfer($type, $transactionId);
        if (!is_null($instance)) {
            return $instance;
        }
        $this->transfers = $this->transfers->create($data);
        $this->processWallets();
        return $this->transfers;
    }

    /**
     * Process Wallets
     *
     * @return void
     */
    protected function processWallets()
    {
        $this->walletLockforUpdate();
        $this->fromWallet->amount -= $this->amount;
        $this->fromWallet->save();
        $this->toWallet->amount += $this->amount;
        $this->toWallet->save();
        $walletLogger = new WalletLogger();
        $walletLogger->log($this);
    }

    /**
     * Lock Wallet
     *
     * @return void
     */
    protected function walletLockforUpdate()
    {
        $this->fromWallet = Wallet::whereId($this->fromWallet->id)->lockForUpdate()->first();
        $this->toWallet = Wallet::whereId($this->toWallet->id)->lockForUpdate()->first();
    }

    /**
     * Set Amount
     *
     * @param numeric $amount
     * 
     * @return TransferService
     */
    public function setAmount($amount)
    {
        $this->verifyAmount($amount);
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set Wallet from
     *
     * @param Wallet $wallet
     * 
     * @return TransferService
     */
    public function setWalletFrom(Wallet $wallet)
    {
        $this->fromWallet = $wallet;
        return $this;
    }

    /**
     * Set Wallet to
     *
     * @param Wallet $wallet
     * 
     * @return TransferService
     */
    public function setWalletTo(Wallet $wallet)
    {
        $this->toWallet = $wallet;
        return $this;
    }

    /**
     * From Wallet
     *
     * @return Wallet
     */
    public function fromWallet(): Wallet
    {
        return $this->fromWallet;
    }

    /**
     * To Wallet
     *
     * @return Wallet
     */
    public function toWallet(): Wallet
    {
        return $this->toWallet;
    }

    /**
     * Transfers
     *
     * @return Transfers
     */
    public function getTransfer(): Transfers
    {
        return $this->transfers;
    }

    /**
     * Verify Wallet
     *
     * @return void|RuntimeException
     */
    protected function verifyWallet()
    {
        if ($this->fromWallet->id === $this->toWallet->id) {
            throw new \RuntimeException("Could not transfer to same wallet!");
        }
    }

    /**
     * Verify Amount
     *
     * @param numeric $amount
     * 
     * @return void
     */
    protected function verifyAmount($amount)
    {
        if ($amount <= 0) {
            throw new \RuntimeException("Could not transfer negative amounts!");
        }
    }
}
