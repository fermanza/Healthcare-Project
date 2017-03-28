<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Practice::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'code' => $faker->word,
    ];
});

$factory->define(App\PositionType::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Person::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
    ];
});

$factory->define(App\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'code' => $faker->word,
    ];
});

$factory->define(App\Employee::class, function (Faker\Generator $faker) {
    return [
        'person_id' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'type' => $faker->word,
        'is_full_time' => $faker->boolean,
    ];
});

$factory->define(App\Division::class, function (Faker\Generator $faker) {
    return [
        'group_id' => function () {
            return factory(App\Group::class)->create()->id;
        },
        'name' => $faker->word,
        'code' => $faker->word,
        'is_jv' => $faker->boolean,
    ];
});

$factory->define(App\Account::class, function (Faker\Generator $faker) {
    return [
        'division_id' => function () {
            return factory(App\Division::class)->create()->id;
        },
        'site_code' => $faker->randomNumber,
        'name' => $faker->word,
        'city' => $faker->city,
        'state' => $faker->state,
        'start_date' => $faker->dateTime,
    ];
});

$factory->define(App\AccountEmployee::class, function (Faker\Generator $faker) {
    return [
        'account_id' => function () {
            return factory(App\Account::class)->create()->id;
        },
        'employee_id' => function () {
            return factory(App\Employee::class)->create()->id;
        },
        'position_type_id' => function () {
            return factory(App\PositionType::class)->create()->id;
        },
        'is_primary' => $faker->boolean,
    ];
});
