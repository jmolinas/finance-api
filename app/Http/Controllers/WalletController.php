<?php

namespace App\Http\Controllers;

use App\Models\Finance\Wallet;
use Dev\Support\Transformers\JsonApiSerializer;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Wallet Collection
     *
     * @param Request $request
     * @param Wallet $wallet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $request, Wallet $wallet)
    {
        $wallets = $this->transform($wallet->get(), new JsonApiSerializer(), 'wallet', $request->url());
        return $this->get(null, $wallets);
    }

    /**
     * Wallet paymentProcessor
     *
     * @param Request $request
     * @param Wallet $wallet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargesWallet(Request $request, Wallet $wallet)
    {
        $wallets = $this->transform(
            $wallet->with('paymentProcessors')->has('paymentProcessors')->get(),
            new JsonApiSerializer(),
            'wallet',
            $request->url()
        );
        return $this->get(null, $wallets);
    }
}
