<?php

use App\Models\Finance\Ledger;
use App\Models\Finance\Transfers;
use App\Services\Transfer\TransferService;
use App\Services\Transfer\TransfersFetcher;
use App\Models\Finance\Wallet;
use App\Services\Ledger\ReturnsLedger;
use App\Services\Transfer\Ledgers\ReturnsLedgerTransfer;
use Illuminate\Database\Eloquent\Collection;

class LedgerReturnsTest extends TestCase
{
    protected $party1, $party2;

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
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->party1 = $this->party = $this->initParty('affiliate');
        $this->initWallet('returns');
        $this->party2 = $this->party = $this->initParty('affiliate');
        $this->initWallet('returns');
    }

    /**
     * Test Collection Ledger
     *
     * @return void
     */
    public function testLedgerReturnsCreate()
    {
        $partyId = config('settings.burgerprints_party_id');
        $fromWallet = Wallet::typeOf('order')->wherePartyId($partyId)->firstOrFail();
        $toWallet = Wallet::typeOf('collection')->wherePartyId($partyId)->firstOrFail();
        $parties = new Collection([$this->party1, $this->party2]);
        $amount = 0;
        factory(Transfers::class, 2)
            ->create(
                [
                    'from_wallet_id' => $fromWallet->id,
                    'to_wallet_id' => $toWallet->id,
                    'metadata' => json_encode($this->metadata),
                    'type' => 'return'
                ]
            )
            ->each(
                function ($transfer) use ($parties, &$amount) {
                    $parties = $parties->pluck('id')->toArray();
                    $transfer->party_id = $parties[array_rand($parties)];
                    $amount += $transfer->amount;
                    $transfer->save();
                }
            );

        $fromWallet->amount -= $amount;
        $fromWallet->save();

        $toWallet->amount += $amount;
        $toWallet->save();

        $ledgerAmount = Transfers::typeOf('return')->sum('amount');
        $ledgerService = new ReturnsLedger(new Ledger(), new TransfersFetcher(new Transfers()));
        $ledgers = $ledgerService->process();
        $sumLedgers = $ledgers->sum(function ($ledger) {
            return $ledger->amount;
        });
        $this->assertEquals($ledgerAmount, $sumLedgers);

        $transferLedger = new ReturnsLedgerTransfer(new TransferService(new Wallet(), new Transfers()));
        $transfers = $transferLedger->transferCollection($ledgers);
        $sumTransfers = $transfers->sum(function ($transfer) {
            return $transfer->amount;
        });
        $this->assertEquals($sumLedgers, $sumTransfers);
    }
}
