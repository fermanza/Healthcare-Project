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
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
    ];
});

$factory->define(App\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'code' => $faker->word,
    ];
});

$factory->define(App\EmployementStatus::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\Employee::class, function (Faker\Generator $faker) {
    return [
        'personId' => function () {
            return factory(App\Person::class)->create()->id;
        },
        'employementStatusId' => function () {
            return factory(App\EmployementStatus::class)->create()->id;
        },
        'employeeType' => $faker->word,
        'isFullTime' => $faker->boolean,
    ];
});

$factory->define(App\Division::class, function (Faker\Generator $faker) {
    return [
        'groupId' => function () {
            return factory(App\Group::class)->create()->id;
        },
        'name' => $faker->word,
        'code' => $faker->word,
        'isJv' => $faker->boolean,
    ];
});

$factory->define(App\Account::class, function (Faker\Generator $faker) {
    return [
        'divisionId' => function () {
            return factory(App\Division::class)->create()->id;
        },
        'name' => $faker->word,
        'siteCode' => $faker->randomNumber,
        'photoPath' => $faker->image,
        'googleAddress' => $faker->address,
        'street' => $faker->streetName,
        'number' => $faker->randomNumber,
        'city' => $faker->city,
        'state' => $faker->state,
        'zipCode' => $faker->randomNumber,
        'country' => $faker->country,
        'startDate' => $faker->dateTime,
        'physiciansNeeded' => $faker->randomNumber,
        'appsNeeded' => $faker->randomNumber,
        'physicianHoursPerMonth' => $faker->randomNumber,
        'appHoursPerMonth' => $faker->randomNumber,
        'pressRelease' => $faker->boolean,
        'pressReleaseDate' => $faker->date,
        'managementChangeMailers' => $faker->boolean,
        'recruitingMailers' => $faker->boolean,
        'emailBlast' => $faker->boolean,
        'purlCampaign' => $faker->boolean,
        'marketingSlick' => $faker->boolean,
        'collaborationRecruitingTeam' => $faker->boolean,
        'collaborationRecruitingTeamNames' => $faker->firstName,
        'compensationGrid' => $faker->boolean,
        'compensationGridBonuses' => $faker->sentence,
        'recruitingIncentives' => $faker->boolean,
        'recruitingIncentivesDescription' => $faker->sentence,
        'locumCompaniesNotified' => $faker->boolean,
        'searchFirmsNotified' => $faker->boolean,
        'departmentsCoordinated' => $faker->boolean,
    ];
});

$factory->define(App\FileType::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\FileStatus::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(App\File::class, function (Faker\Generator $faker) {
    return [
        'fileTypeId' => function () {
            return factory(App\FileType::class)->create()->id;
        },
        'fileStatusId' => function () {
            return factory(App\FileStatus::class)->create()->id;
        },
        'filename' => $faker->sentence,
        'path' => $faker->image,
    ];
});
