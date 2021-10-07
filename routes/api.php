<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Models\Finance\Transfers;
use App\Models\Finance\Ledger;
use App\Models\Finance\WalletLogs;

$router->get(
    '/',
    function () use ($router) {
        return $router->app->version();
    }
);
$router->group(
    [
        'prefix' => 'api/v1',
    ],
    function () use ($router) {
        // Consumer Endpoint
        $router->post('process', 'ConsumerEndpointController@process');

        $router->group(
            [
                'middleware' => [
                    'auth',
                ]
            ],
            function () use ($router) {
                $transfers = Transfers::class;
                $ledger = Ledger::class;
                $walletLogs = WalletLogs::class;

                // Orders
                $router->post('orders/chargeback', 'OrdersController@chargeback');
                $router->post('orders/refund', 'OrdersController@refund');

                $router->get(
                    'orders',
                    [
                        'uses' => 'OrdersController@collection',
                        'middleware' => [
                            "api:{$transfers}",
                        ]
                    ]
                );

                $router->get(
                    'orders/history',
                    [
                        'uses' => 'OrdersController@history',
                        'middleware' => [
                            "api:{$walletLogs}",
                        ]
                    ]
                );

                $router->get(
                    'orders/history/details',
                    [
                        'uses' => 'OrdersController@logs',
                        'middleware' => [
                            "api:{$walletLogs}",
                        ]
                    ]
                );

                //Currency List
                $router->get('currencies', 'CurrenciesController@list');

                // Wallet Collection
                $router->get(
                    'wallets',
                    [
                        'uses' => 'WalletController@collection',
                        'middleware' => ['wallet']
                    ]
                );

                // Wallet Collection
                $router->get(
                    'wallets/payment-processors',
                    [
                        'uses' => 'WalletController@chargesWallet',
                    ]
                );

                // Sales Ledgers
                $router->get(
                    'ledgers',
                    [
                        'uses' => 'Ledgers\LedgerController@collection',
                        'middleware' => [
                            "api:{$ledger}",
                        ]
                    ]
                );

                $router->get(
                    'transfers',
                    [
                        'uses' => 'Transfers\TransfersController@collection',
                        'middleware' => [
                            "api:{$transfers}",
                        ]
                    ]
                );

                $router->get(
                    'accounts',
                    [
                        'uses' => 'Transfers\TransfersController@accounts',
                        'middleware' => [
                            "api:{$transfers}",
                        ]
                    ]
                );


                // Transaction Matching
                $router->post(
                    'shipping/billing',
                    [
                        'uses' => 'TransactionController@matchShippingBilling'
                    ]
                );
            }
        );
    }
);
