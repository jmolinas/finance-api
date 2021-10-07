<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserPartyWalletDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'order',
            'collection',
            'sale',
            'returns'
        ];

        factory(App\Models\Party::class)
            ->create(['type' => 'user'])
            ->each(
                function ($party) use ($types) {
                    $party->user()
                        ->save(
                            factory(App\Models\User::class)->make(
                                [
                                    'name' => "BURGER PRINTS",
                                    'email' => "finance@burgerprints.com",
                                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                                ]
                            )
                        );
                    foreach ($types as $type) {
                        $party->wallets()
                            ->save(
                                factory(App\Models\Finance\Wallet::class)->make(
                                    [
                                        'currency_code' => 'USD',
                                        'type' => $type,
                                        'amount' => 0
                                    ]
                                )
                            );
                    }
                }
            );
    }
}
