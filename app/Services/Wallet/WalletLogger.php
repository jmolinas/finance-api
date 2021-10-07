<?php

namespace App\Services\Wallet;

use App\Models\Finance\Wallet;
use App\Models\Finance\Transfers;
use App\Models\Finance\WalletLogs;
use App\Services\Transfer\TransferService;

class WalletLogger
{
    /**
     * Create WalletLogs
     *
     * @param Wallet    $wallet
     * @param Transfers $transfer
     * @param float   $amount
     * 
     * @return WalletLogs
     */
    protected function store(Wallet $wallet, Transfers $transfer, $amount) : WalletLogs
    {
        return WalletLogs::create(
            [
                'wallet_id' => $wallet->id,
                'transfer_id' => $transfer->id,
                'amount' => $amount,
                'running_balance' => $wallet->amount
            ]
        );
    }

    /**
     * Create WalletLogs
     *
     * @return array
     */
    public function log(TransferService $walletTransfers)
    {
        $transfers = $walletTransfers->getTransfer();
        $fromWallet = $walletTransfers->fromWallet();
        $toWallet = $walletTransfers->toWallet();

        $fromWalletLog = $this->store($fromWallet, $transfers, -$transfers->amount);
        $toWalletLog = $this->store($toWallet, $transfers, $transfers->amount);
        return [
            'from_wallet' => $fromWalletLog,
            'to_wallet' => $toWalletLog
        ];
    }
}
