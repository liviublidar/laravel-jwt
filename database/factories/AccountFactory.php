<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'domain' => $faker->lastName,
        'main_email' => $faker->unique()->safeEmail,
        'code' => base64_encode($faker->unique()->text(32)),
        'suspended' => (int) $faker->boolean(50),
    ];
});
