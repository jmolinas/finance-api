<?php

use Laravel\Lumen\Testing\WithoutMiddleware;

class WalletTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * Test List Wallets
     *
     * @return void
     */
    public function testCanListWallets()
    {
        $this->json('GET', '/api/v1/wallets')->seeStatusCode(200);
    }
}
