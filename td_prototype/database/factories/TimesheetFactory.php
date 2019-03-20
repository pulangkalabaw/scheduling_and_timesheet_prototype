<?php

use Faker\Generator as Faker;
echo now();

$factory->define(App\Timesheet::class, function (Faker $faker) {
    return [
		'user_id' => 1,
		'td_in' => now(),
		'td_out' => now(),
		'sched_id' => 1111,
		'late' => 0,
		'undertime' => 0,
		'overtime' => 0,
		'worked_mins' => 0,
		'status' => 0,
    ];
});
