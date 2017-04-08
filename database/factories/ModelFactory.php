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
        'recruiter_id' => function () {
            return factory(App\Employee::class)->create()->id;
        },
        'manager_id' => function () {
            return factory(App\Employee::class)->create()->id;
        },
        'practice_id' => function () {
            return factory(App\Practice::class)->create()->id;
        },
        'division_id' => function () {
            return factory(App\Division::class)->create()->id;
        },
        'name' => $faker->word,
        'site_code' => $faker->randomNumber,
        'photo_path' => $faker->image,
        'google_address' => $faker->address,
        'street' => $faker->streetName,
        'number' => $faker->randomNumber,
        'city' => $faker->city,
        'state' => $faker->state,
        'zip_code' => $faker->randomNumber,
        'country' => $faker->country,
        'start_date' => $faker->dateTime,
        'physicians_needed' => $faker->randomNumber,
        'apps_needed' => $faker->randomNumber,
        'physician_hours_per_month' => $faker->randomNumber,
        'app_hours_per_month' => $faker->randomNumber,
        'press_release' => $faker->boolean,
        'press_release_date' => $faker->date,
        'management_change_mailers' => $faker->boolean,
        'recruiting_mailers' => $faker->boolean,
        'email_blast' => $faker->boolean,
        'purl_campaign' => $faker->boolean,
        'marketing_slick' => $faker->boolean,
        'collaboration_recruiting_team' => $faker->boolean,
        'collaboration_recruiting_team_names' => $faker->firstName,
        'compensation_grid' => $faker->boolean,
        'compensation_grid_bonuses' => $faker->sentence,
        'recruiting_incentives' => $faker->boolean,
        'recruiting_incentives_description' => $faker->sentence,
        'locum_companies_notified' => $faker->boolean,
        'search_firms_notified' => $faker->boolean,
        'departments_coordinated' => $faker->boolean,
    ];
});

$factory->define(App\File::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'filename' => $faker->sentence,
        'path' => $faker->image,
    ];
});
