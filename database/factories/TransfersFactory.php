<?php

use Faker\Generator as Faker;
use App\Models\Finance\Transfers;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Transfers::class, function (Faker $faker) {
    $itemId = $faker->uuid;
    $orderId = $faker->uuid;
    $amount = $faker->randomFloat(2, 25, 150);
    return [
        'type' => 'sale',
        'details' => "Sale transfer amounting to {$amount} from: order# {$orderId} order item# {$itemId}",
        'order_id' => $orderId,
        'order_item_id' => $itemId,
        'amount' => $amount
    ];
});
