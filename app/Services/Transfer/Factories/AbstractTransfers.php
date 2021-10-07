<?php

namespace App\Services\Transfer\Factories;

use App\Models\Finance\Transfers;
use App\Models\Finance\Wallet;
use App\Models\Party;
use App\Services\Transfer\TransferService;
use App\Models\PaymentProcessors;

abstract class AbstractTransfers implements TransfersInterface
{
    protected $transfers;
    protected $metadata = [];
    protected $partyId;
    protected static $type;
    protected $party;

    /**
     * AbstractPayloadTransfer
     *
     * @param TransferService $transfers
     */
    public function __construct(TransferService $transfers)
    {
        $this->transfers = $transfers;
        $this->partyId =  config('settings.burgerprints_party_id');
    }

    /**
     * Set metadata
     *
     * @param array $metadata
     * 
     * @return Static
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = json_encode($metadata);
        return $this;
    }

    /**
     * Get Wallet from
     *
     * @return Wallet
     */
    protected function getWalletFrom(): Wallet
    {
        return Wallet::typeOf('order')
            ->wherePartyId($this->partyId)
            ->firstOrfail();
    }

    /**
     * Get Wallet to
     *
     * @return Wallet
     */
    protected function getWalletTo(): Wallet
    {
        return Wallet::typeOf('collection')
            ->wherePartyId($this->partyId)
            ->firstOrfail();
    }

    /**
     * Charge Wallet From
     *
     * @param string $merchant
     * 
     * @return Wallet
     */
    protected function chargeWalletFrom($merchant): Wallet
    {
        $merchant = PaymentProcessors::whereName($merchant)->firstOrFail();
        $this->transfers->setParty(Party::firstOrFail($merchant->party_id));
        return Wallet::typeOf('charges')
            ->wherePartyId($merchant->party_id)
            ->firstOrfail();
    }

    /**
     * Set Party
     *
     * @param Party $party
     * 
     * @return static
     */
    public function setParty(Party $party)
    {
        $this->party = $party;
        $this->transfers->setParty($party);
        return $this;
    }

    /**
     * Process sales transfer
     *
     * @param string $orderId
     * @param string $itemId
     * @param string $campaignId
     * @param numeric $amount
     * @param string $productId
     * @param string $sku
     * @param string $transactionDate
     * 
     * @return Transfers
     */
    public function process($orderId, $itemId, $campaignId, $amount, $productId, $sku = null, $transactionDate = null): Transfers
    {
        $type = static::$type;
        if (empty($this->metadata)) {
            throw new \RuntimeException("Invalid or empty metadata");
        }

        if ($type === null && empty($party = $this->transfers->getParty())) {
            if ($party->type !== $type) {
                throw new \RuntimeException("Empty or Invalid Party type");
            }
        }
        $item = !empty($itemId) ? ", Item#{$itemId}" : '';
        return $this->transfers
            ->setWalletFrom($this->getWalletFrom())
            ->setWalletTo($this->getWalletTo())
            ->setAmount($amount)
            ->create(
                $type,
                $itemId,
                lcfirst($type) . " from Order#{$orderId}{$item}",
                $this->metadata,
                $orderId,
                $campaignId,
                $productId,
                $sku,
                $transactionDate
            );
    }

    /**
     * Reverse Charge
     *
     * @param Transfers $order
     * 
     * @return Transfers
     */
    public function reverseCharge(Transfers $order)
    {
        $type = static::$type;
        $orderId = $order->order_id;
        $fromWallet = $this->chargeWalletFrom($order->metadata->payment->method);

        return $this->transfers
            ->setWalletFrom($fromWallet)
            ->setWalletTo($this->getWalletTo())
            ->setAmount($order->metadata->payment->service_fee)
            ->create(
                $type,
                $order->transaction_id,
                lcfirst($type) . " from Order#{$orderId}",
                $this->metadata,
                $orderId,
                $order->campaign_id,
                null
            );
    }

    /**
     * Mechant Charge
     *
     * @param Transfers $order
     * 
     * @return Transfers
     */
    public function merchantCharge(Transfers $order, $amount)
    {
        $type = static::$type;
        $orderId = $order->order_id;
        $toWallet = $this->chargeWalletFrom($order->metadata->payment->method);

        return $this->transfers
            ->setWalletFrom($this->getWalletFrom())
            ->setWalletTo($toWallet)
            ->setAmount($amount)
            ->create(
                $type,
                $order->transaction_id,
                lcfirst($type) . " from Order#{$orderId}",
                $this->metadata,
                $orderId,
                $order->campaign_id,
                null
            );
    }
}
