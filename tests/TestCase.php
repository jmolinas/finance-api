<?php

use App\Models\Party;
use App\Models\User;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    protected $party, $user, $fromWallet, $toWallet;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * Init users
     *
     * @return void
     */
    protected function initUsers($type = 'user')
    {
        DB::transaction(function () use ($type) {
            $this->party = factory(Party::class)->create(['type' => $type]);
            $this->user = $this->party
                ->user()
                ->save(factory(User::class)->make());
        });
    }

    /**
     * Init party
     *
     * @return void
     */
    protected function initParty($type = 'user')
    {
        return DB::transaction(function () use ($type) {
            $party = factory(Party::class)->create(['type' => $type]);
            $party->user()
                ->save(factory(User::class)->make());
            return $party;
        });
    }

    /**
     * Init Wallets
     *
     * @return void
     */
    protected function initWallet($type)
    {
        return DB::transaction(function () use ($type) {
            return $this->party->wallets()
                ->create(
                    [
                        'currency_code' => 'USD',
                        'type' => $type,
                        'amount' => 0
                    ]
                );
        });
    }
}
