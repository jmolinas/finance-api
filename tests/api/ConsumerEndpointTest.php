<?php

class ConsumerEndpointTest extends TestCase
{
    /**
     * Test consumer endpoint
     *
     * @return void
     */
    public function testProcessPayload()
    {
        $this->json(
            'POST',
            '/api/v1/process',
            [
                "id" => "BS123-1",
                "product_id" => "ABC-TS-NEW",
                "affiliate_id" => "A7643",
                "campaign_id" => "ABC-123-TS-116",
                "name" => "TeeShirt New",
                "quantity" => 1,
                "sale" => 30,
                "seller_profit" => 35,
                "production_cost" => 20,
                "currency" => "USD",
                "price" => 85,
                "shipping_cost" => 10,
                "merchant_charge" => 5,
                "partner" => "tsc",
                "order" => [
                    "id" => "BS1231",
                    "name" => "James Doe",
                    "email" => "james@doe.net",
                    "delivery_address" => "MI, United States of America",
                    "transaction_date" => "2019-07-12",
                    "amount" => 95,
                ],
                "payment" => [
                    "method" => "paypal_pro",
                    "billing_address" => null,
                    "reference_id" => "sdasd1Afj92Opasj6",
                    "service_fee" => 5,
                    "total_charges" => 5,
                ]
            ]
        )
            ->seeStatusCode(201);
    }

    /**
     * Chargeback endpoint
     *
     * @return void
     */
    public function testOrderRefund()
    {
        $this->json(
            'POST',
            '/api/v1/order/refund',
            [
                'order_id' => 'BS1231',
                'charge' => 1,
                'charge_amount' => 10
            ]
        )->seeStatusCode(202);
    }
}
