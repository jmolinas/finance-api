<?php

namespace App\Services;

use App\Models\Finance\Transfers;
use App\Services\Transfer\Factories\ImmediateReturns;
use App\Services\Transfer\Factories\ReturnsTransfers;
use Illuminate\Support\Facades\DB;

class ReturnOrders
{
    protected $transfers, $immediateReturns, $returns, $charge;

    /**
     * Refunded Orders
     *
     * @param Transfers $transfers
     * @param ImmediateReturns $immediateReturns
     * @param ReturnsTransfers $returns
     */
    public function __construct(
        Transfers $transfers,
        ImmediateReturns $immediateReturns,
        ReturnsTransfers $returns
    ) {
        $this->transfers = $transfers;
        $this->immediateReturns = $immediateReturns;
        $this->returns = $returns;
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
     * Process Returns
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
            ->distinct('order_id')
            ->firstOrFail();
        if ($order->ledger_id === null) {
            $this->transfer($order, $this->immediateReturns);
            return $order;
        }
        $this->transfer($order, $this->returns);
        return $order;
    }

    /**
     * Change Status
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
            ->update(['status' => 'refunded']);
    }

    /**
     * Process Returns
     *
     * @param Transfers $order
     * @param App\Services\Transfer\Factories\AbstractTransfers $transfer
     * 
     * @return void
     */
    protected function transfer(Transfers $order, $transfer)
    {
        return DB::transaction(
            function () use ($order, $transfer) {
                $metadata = (array) $order->metadata;
                $amount = $metadata['order']->amount -
                    $metadata['payment']->service_fee;

                $transfer
                    ->setMetadata($metadata)
                    ->process(
                        $order->order_id,
                        null,
                        $order->campaign_id,
                        $amount
                    );
                $this->changeStatus($order->order_id);
                if ($this->charge) {
                    $transfer->merchantCharge($order, $this->charge);
                    return $transfer;
                }
                $transfer->reverseCharge($order);
                return $transfer;
            }
        );
    }
}
