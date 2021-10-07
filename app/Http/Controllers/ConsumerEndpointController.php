<?php

namespace App\Http\Controllers;

use App\Factories\Transfers\TransactionBuilder;
use App\Services\Transfer\Factories\CollectionTransfers;
use App\Services\Transfer\Factories\PartnerTransfers;
use App\Services\Transfer\Factories\PaymentChargeTransfers;
use App\Services\Transfer\Factories\SaleTransfers;
use App\Services\Transfer\Factories\ShippingTransfers;
use App\Factories\Transfers\TransferFactory;
use Illuminate\Http\Request;

class ConsumerEndpointController extends Controller
{
    /**
     * Process
     *
     * @param Request $request
     * @param PaymentChargeTransfers $charges
     * @param ShippingTransfers $shipping
     * @param PartnerTransfers $partner
     * @param SaleTransfers $sale
     * @param CollectionTransfers $collection
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(
        Request $request,
        PaymentChargeTransfers $charges,
        ShippingTransfers $shipping,
        PartnerTransfers $partner,
        SaleTransfers $sale,
        CollectionTransfers $collection
    ) {
        $schema = config('schema.core');
        $sellerProfit = $request->input('seller_profit');
        $rule = [
            'partner' => "exists:pgsql.{$schema}.partners,machine_name",
            'payment.method'
            => "exists:pgsql.{$schema}.payment_processors,machine_name",
        ];
        if ($sellerProfit > 0) {
            $rule['affiliate_id'] = "exists:pgsql.{$schema}.affiliates,external_id";
        }
        $this->validate(
            $request,
            $rule
        );

        $payload = new TransactionBuilder($request);
        $transfers = new TransferFactory($payload);
        $transfers->process($charges, $shipping, $partner, $sale, $collection);

        return $this->created();
    }
}
