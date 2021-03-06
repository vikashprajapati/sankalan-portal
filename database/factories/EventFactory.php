<?php

use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    $title = $faker->unique()->words(3, true);
    return [
        'title' => $title,
        'slug' => str_slug($title),
        'description' => $faker->paragraph(),
        'rounds' => $faker->numberBetween(1,3),
        'hasQuiz' => true,
    ];
});
$factory->state(App\Event::class, 'withoutQuiz', [
    'hasQuiz' => false,
]);
