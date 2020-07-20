<?php

use Transmissor\Models\Group;
use Transmissor\Models\User;
use Faker\Generator as Faker;

$factory->define(Transmissor\Models\Debt::class, function (Faker $faker) {
    return [
        'from_id' => factory(User::class)->create(),
        'to_id' => factory(User::class)->create(),
        'group_id' => factory(Group::class)->create(),
        'amount' => random_int(0, 200),
        'currency' => 'eur'
    ];
});
