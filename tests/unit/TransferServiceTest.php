<?php

use App\Services\Transfer\TransferService;
use App\Models\Finance\Wallet;
use App\Models\Finance\Transfers;
use App\Models\Finance\WalletLogs;

class TransferServiceTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        $this->initUsers();
        $this->fromWallet = $this->initWallet('order');
        $this->toWallet = $this->initWallet('collection');
    }

    /**
     * Test WalletTransferService
     *
     * @return void
     */
    public function testTransfers()
    {
        $fromWalletId = $this->fromWallet->id;
        $toWalletId = $this->toWallet->id;
        $transferService = new TransferService(new Wallet(), new Transfers());
        $transfer = $transferService
            ->setWalletFrom($this->fromWallet)
            ->setWalletTo($this->toWallet)
            ->setAmount(100)
            ->create('sale', 'Test transfer amount of 100');
        
        $fromWallet = Wallet::find($fromWalletId);
        // Test from Wallet Balance
        $this->assertEquals(-100, $fromWallet->amount);
        // Test to Wallet Balance
        $this->assertEquals(100, Wallet::find($toWalletId)->amount);
        // Test Transfer Amount
        $this->assertEquals(100, $transfer->amount);
        // Test WalletLogs Running Balance
        $this->assertEquals(
            $fromWallet->amount, 
            WalletLogs::where('wallet_id', $fromWallet->id)->first()->running_balance
        );
    }
}
