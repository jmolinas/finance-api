<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partners = [
            [
                'machine_name' => 'tsc',
                'name' => 'TSC'
            ],
            [
                'machine_name' => 'champy',
                'name' => 'Champy'
            ],
            [
                'machine_name' => 'canvas champ',
                'name' => 'Canvas Champ'
            ],
            [
                'machine_name' => 'canvas chambo',
                'name' => 'Canvas Chambo'
            ],
            [
                'machine_name' => 'dhl',
                'name' => 'DHL'
            ],
            [
                'machine_name' => 'joy',
                'name' => 'Joy'
            ],
            [
                'machine_name' => 'scalable_press',
                'name' => 'Scalable Press'
            ]
        ];

        factory(App\Models\Party::class, 6)
            ->create(['type' => 'partner'])
            ->each(
                function ($party, $key) use ($partners) {
                    $party->partners()
                        ->save(
                            factory(App\Models\Partners::class)->make(
                                [
                                    'name' => $partners[$key]['name'],
                                    'machine_name' => $partners[$key]['machine_name']
                                ]
                            )
                        );
                    $party->wallets()
                        ->save(
                            factory(App\Models\Finance\Wallet::class)->make(
                                [
                                    'currency_code' => 'USD',
                                    'type' => 'settlement',
                                    'amount' => 0
                                ]
                            )
                        );
                }
            );
    }
}
