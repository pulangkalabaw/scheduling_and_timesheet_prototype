<?php

use Illuminate\Database\Seeder;

class TimesheerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(App\Timesheet::class, 11)->create();
    }
}
