<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentProcessorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentProcessors = [
            [
                'name' => 'Stripe',
                'machine_name' => 'stripe'
            ],
            [
                'name' => 'Paypal Pro',
                'machine_name' => 'paypal_pro'
            ],
            [
                'name' => 'Paypal',
                'machine_name' => 'paypal'
            ],
            [
                'name' => 'Bank of America',
                'machine_name' => 'bank_of_usa'
            ],
            [
                'name' => 'Anet',
                'machine_name' => 'anet'
            ],
            [
                'name' => 'Payoneer',
                'machine_name' => 'payoneer'
            ]
        ];

        factory(App\Models\Party::class, 5)
            ->create(['type' => 'payment_processor'])
            ->each(
                function ($party, $key) use ($paymentProcessors) {
                    $party->paymentProcessors()
                        ->save(
                            factory(App\Models\PaymentProcessors::class)->make(
                                [
                                    'name' => $paymentProcessors[$key]['name'],
                                    'machine_name' => $paymentProcessors[$key]['machine_name']
                                ]
                            )
                        );
                    $party->wallets()
                        ->save(
                            factory(App\Models\Finance\Wallet::class)->make(
                                [
                                    'currency_code' => 'USD',
                                    'type' => 'charges',
                                    'amount' => 0
                                ]
                            )
                        );
                    $party->wallets()
                        ->save(
                            factory(App\Models\Finance\Wallet::class)->make(
                                [
                                    'currency_code' => 'USD',
                                    'type' => 'chargeback',
                                    'amount' => 0
                                ]
                            )
                        );
                }
            );
    }
}
