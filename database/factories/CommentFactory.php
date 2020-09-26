<?php

/*
|--------------------------------------------------------------------------
| Comment
|--------------------------------------------------------------------------
*/


$factory->define(
    Transmissor\Models\Comment::class, function (Generator $faker) {
        return [
        'user_id' => rand(1, 10),
        'post_id' => rand(1, 25),
        'body'    => $faker->paragraph
        ];
    }
);
