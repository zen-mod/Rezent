<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Build;
use Faker\Generator as Faker;

$factory->define(Build::class, function (Faker $faker) {
    return [
        'commit_hash' => $faker->sha1,
        'branch' => $faker->slug,
        'successful' => $faker->boolean(),
    ];
});
