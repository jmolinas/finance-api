<?php

namespace App\Services;

use App\Services\Transfer\Factories\ChargebackTransfers;
use App\Models\Finance\Transfers;
use Illuminate\Support\Facades\DB;

class ChargebackOrders
{
    protected $chargeback, $transfers, $orders, $charge;

    /**
     * Chargeback Orders
     *
     * @param ChargebackTransfers $chargeback
     * @param Transfers $transfers
     */
    public function __construct(ChargebackTransfers $chargeback, Transfers $transfers)
    {
        $this->chargeback = $chargeback;
        $this->transfers = $transfers;
    }

    /**
     * Charge Amount
     *
     * @param numeric $amount
     * 
     * @return void
     */
    public function charge($amount)
    {
        $this->charge = $amount;
    }

    /**
     * Process Chargebacks
     *
     * @param string $orderId
     * 
     * @return Transfers
     */
    public function process($orderId)
    {
        $order = $this->transfers
            ->whereType('collection')
            ->whereOrderId($orderId)
            ->whereNotNull('ledger_id')
            ->distinct('order_id')
            ->firstOrFail();
        $this->processChargebacks($order, $this->chargeback);

        return $order;
    }

    /**
     * Update status
     *
     * @param string $orderId
     * 
     * @return void
     */
    protected function changeStatus($orderId)
    {
        Transfers::whereOrderId($orderId)
            ->whereIn(
                'type',
                [
                    'sale',
                    'collection',
                    'production_cost',
                    'shipping',
                    'charges'
                ]
            )
            ->update(['status' => 'chargedback']);
    }

    /**
     * Process Chargebacks
     *
     * @param Transfers $order
     * @param ChargebackTransfers $chargeback
     * 
     * @return void
     */
    protected function processChargebacks(
        Transfers $order,
        ChargebackTransfers $chargeback
    ) {
        return DB::transaction(
            function () use ($order, $chargeback) {
                $metadata = (array) $order->metadata;
                $amount = $metadata['order']->amount -
                    $metadata['payment']->service_fee;

                $transfer = $chargeback
                    ->setMetadata($metadata)
                    ->process(
                        $order->order_id,
                        null,
                        $order->campaign_id,
                        $amount
                    );
                if ($this->charge) {
                    $chargeback->merchantCharge($order, $this->charge);
                    return $transfer;
                }
                $chargeback->reverseCharge($order);
                return $transfer;
            }
        );
    }
}
