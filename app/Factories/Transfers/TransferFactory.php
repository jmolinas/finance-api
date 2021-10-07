<?php

namespace App\Factories\Transfers;

use App\Models\Affiliates;
use App\Models\Finance\Transfers;
use App\Models\Partners;
use App\Models\Party;
use App\Models\PaymentProcessors;
use App\Services\Transfer\Factories\CollectionTransfers;
use App\Services\Transfer\Factories\PartnerTransfers;
use App\Services\Transfer\Factories\PaymentChargeTransfers;
use App\Services\Transfer\Factories\SaleTransfers;
use App\Services\Transfer\Factories\ShippingTransfers;
use App\Services\Transfer\Factories\TransfersInterface;
use Illuminate\Support\Facades\DB;

class TransferFactory
{
    protected $payload;

    /**
     * Transfer Factory
     *
     * @param TransactionBuilder $payload
     */
    public function __construct(TransactionBuilder $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Do Transfer
     *
     * @param PaymentChargeTransfers $charges
     * @param ShippingTransfers $shipping
     * @param PartnerTransfers $partner
     * @param SaleTransfers $sale
     * @param CollectionTransfers $collection
     * 
     * @return void
     */
    public function process(
        PaymentChargeTransfers $charges,
        ShippingTransfers $shipping,
        PartnerTransfers $partner,
        SaleTransfers $sale,
        CollectionTransfers $collection
    ) {
        DB::transaction(
            function () use (
                $charges,
                $shipping,
                $partner,
                $sale,
                $collection
            ) { 
                $this->doMerchantCharges($charges)
                    ->doShippingRecord($shipping)
                    ->doProductionCostRecord($partner)
                    ->doSale($sale)
                    ->doSellerProfitRecord($collection);
            },
            10
        );
    }

    /**
     * Do merchant Charges
     *
     * @param PaymentChargeTransfers $charges
     * 
     * @return void
     */
    public function doMerchantCharges(PaymentChargeTransfers $charges)
    {
        $payload = $this->payload;
        $paymentProcessor = PaymentProcessors::whereMachineName(
            $payload->paymentMethod
        )->firstOrFail();
        $this->create(
            $charges,
            $payload->merchantCharge,
            $paymentProcessor->party_id
        );
        return $this;
    }

    /**
     * Do Shipping Record
     *
     * @param ShippingTransfers $shipping
     * 
     * @return void
     */
    public function doShippingRecord(ShippingTransfers $shipping)
    {
        $payload = $this->payload;
        if ($payload->shipping > 0) {
            $shipper = Partners::whereMachineName('dhl')->firstOrFail();
            $this->create($shipping, $payload->shipping, $shipper->party_id);
        }
        return $this;
    }

    /**
     * Do Production Record
     *
     * @param PartnerTransfers $partner
     * 
     * @return void
     */
    public function doProductionCostRecord(PartnerTransfers $partner)
    {
        $payload = $this->payload;
        $partners = Partners::whereMachineName(
            $payload->partner
        )->firstOrFail();
        $this->create($partner, $payload->productionCost, $partners->party_id);
        return $this;
    }

    /**
     * Do Sale
     *
     * @param SaleTransfers $sale
     * 
     * @return void
     */
    public function doSale(SaleTransfers $sale)
    {
        $payload = $this->payload;
        if ($payload->sale > 0) {
            $this->create($sale, $payload->sale, config('settings.burgerprints_party_id'));
        }
        return $this;
    }

    /**
     * Do Seller Profit
     *
     * @param CollectionTransfers $collection
     * 
     * @return void
     */
    public function doSellerProfitRecord(CollectionTransfers $collection)
    {
        $payload = $this->payload;
        if ($this->payload->sellerProfit > 0) {
            $affiliate = Affiliates::whereExternalId(
                $payload->affiliateId
            )->firstOrFail();
            $this->create($collection, $payload->sellerProfit, $affiliate->party_id);
        }
        return $this;
    }

    /**
     * Create Transfers
     *
     * @param TransfersInterface $transfers
     * @param numeric $amount
     * @param Party $party
     * 
     * @return Transfers
     */
    protected function create(
        TransfersInterface $transfers,
        $amount,
        $partyId
    ): Transfers {
        $payload = $this->payload;
        unset($payload->metadata['id']);
        $party = Party::findOrFail($partyId);
        return $transfers
            ->setParty($party)
            ->setMetadata($this->payload->metadata)
            ->process(
                $payload->orderId,
                $payload->itemId,
                $payload->campaignId,
                $amount,
                $payload->productId,
                $payload->sku,
                $payload->transactionDate
            );
    }
}
