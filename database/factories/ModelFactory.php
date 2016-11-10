<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'facebook_id'   => $faker->unique()->randomNumber(4),
        'name'          => $faker->firstName() . ' ' . $faker->lastName(),
        'email'         => $faker->unique()->safeEmail,
        'credits'       => $faker->numberBetween(0, 100000),
    ];
});

$factory->define(App\UserStats::class, function (Faker\Generator $faker) {
    return [
        'user_id'           => factory(App\User::class)->create()->id,
        'bets_won'          => $faker->numberBetween(0, 100000),
        'bets_lost'         => $faker->numberBetween(0, 100000),
        'bets_complete'     => $faker->numberBetween(0, 100000),
        'weekly_streak'     => $faker->numberBetween(0, 100000),
        'monthly_streak'    => $faker->numberBetween(0, 100000),
        'alltime_streak'    => $faker->numberBetween(0, 100000),
        'weekly_wins'       => $faker->numberBetween(0, 100000),
        'monthly_wins'      => $faker->numberBetween(0, 100000),
        'alltime_wins'      => $faker->numberBetween(0, 100000),
        'redis_update'      => 1,
    ];
});
