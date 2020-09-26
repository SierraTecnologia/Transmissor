<?php

/*
 * --------------------------------------------------------------------------
 * Notification Factory
 * --------------------------------------------------------------------------
*/

$factory->define(
    Transmissor\Models\Notification::class, function (Faker\Generator $faker) {
        return [
        'user_id' => 1,
        'flag' => 'info',
        'uuid' => 'lksjdflaskhdf',
        'title' => 'Testing',
        'details' => 'Your car has been impounded!',
        'is_read' => 0,
        ];
    }
);
