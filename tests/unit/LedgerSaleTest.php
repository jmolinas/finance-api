<?php

use App\Models\Finance\Ledger;
use App\Models\Finance\Transfers;
use App\Services\Ledger\SalesLedger;
use App\Services\Transfer\Ledgers\SalesLedgerTransfer;
use App\Services\Transfer\TransferService;
use App\Services\Transfer\TransfersFetcher;
use App\Models\Finance\Wallet;

class LedgerSaleTest extends TestCase
{
    protected $metadata = [
        "order_item_id" => "ABC121-12-1",
        'id' =>  "ABC121-123",
        "affiliate_id" => "AASJ-123123",
        "campaign_id" => "ASH-1231",
        "name" => "TeeShirt",
        "quantity" => 1,
        "order" => [
            "id" => "ABC121-12",
            "name" => "James Doe",
            "email" => "james@doe.net",
            "delivery_address" => "MI, United States of America",
            "transaction_date" => "2019-07-12",
            "sale" => 10.22,
            "currency" => "USD"
        ],
        "payment" => [
            "method" => "paypal",
            "billing_address" => null,
            "reference_id" => "sdasd1Afj92Opasj6"
        ]
    ];

    /**
     * Test Sales Ledger
     *
     * @return void
     */
    public function testLedgerSaleCreate()
    {
        $partyId = config('settings.burgerprints_party_id');
        $fromWallet = Wallet::typeOf('order')->wherePartyId($partyId)->firstOrFail();
        $toWallet = Wallet::typeOf('collection')->wherePartyId($partyId)->firstOrFail();
        $amount = 0;
        factory(Transfers::class, 20)->create(
            [
                'from_wallet_id' => $fromWallet->id,
                'to_wallet_id' => $toWallet->id,
                'party_id' => $partyId,
                'metadata' => json_encode($this->metadata)
            ]
        )->each(
            function ($transfer) use (&$amount) {
                $amount += $transfer->amount;
            }
        );;
        $ledgerService = new SalesLedger(new Ledger(), new TransfersFetcher(new Transfers()));
        $sales = Transfers::typeOf('sale')->sum('amount');
        $fromWallet->amount -= $amount;
        $fromWallet->save();

        $toWallet->amount += $amount;
        $toWallet->save();
        $ledger = $ledgerService->process();
        $this->assertEquals($sales, $ledger->amount);

        $transferLedger = new SalesLedgerTransfer(new TransferService(new Wallet(), new Transfers()));
        $transfer = $transferLedger->transfer($ledger);
        $this->assertEquals($transfer->amount, $ledger->amount);
    }
}
