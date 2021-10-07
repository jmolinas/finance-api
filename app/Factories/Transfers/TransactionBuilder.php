<?php

namespace App\Factories\Transfers;

use Illuminate\Http\Request;

class TransactionBuilder
{
    public $metadata,
        $orderId,
        $itemId,
        $campaignId ,
        $shipping,
        $transactionDate,
        $sale,
        $affiliateId,
        $productionCost,
        $partner,
        $paymentMethod,
        $merchantCharge,
        $sellerProfit;

    /**
     * Transaction Builder
     *
     * @param Request $request
     * 
     */
    public function __construct(Request $request)
    {
        $this->metadata = $request->except(
            [
                'sale',
                'seller_profit',
                'production_cost',
                'campaign_id',
                'merchant_charge',
                'product_id',
                'sku'
            ]
        );

        $this->orderId = $request->input('order.id');
        $this->itemId = $request->input('id');
        $this->campaignId = $request->input('campaign_id');
        $this->shipping = $request->input('shipping_cost', 0);
        $this->transactionDate = $request->input('order.transaction_date');
        $this->sale = $request->input('sale');
        $this->sellerProfit = $request->input('seller_profit');
        $this->affiliateId = $request->input('affiliate_id');
        $this->productionCost = $request->input('production_cost');
        $this->partner = $request->input('partner');
        $this->paymentMethod = $request->input('payment.method');
        $this->merchantCharge = $request->input('merchant_charge');
        $this->productId = $request->input('product_id');
        $this->sku = $request->input('sku');
    }
}
