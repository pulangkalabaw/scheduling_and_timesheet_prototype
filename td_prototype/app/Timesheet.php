<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $table = "timesheets";
	protected $guarded = [];

    public function getUser() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function getSchedule() {
        return $this->hasOne('App\Schedule', 'sched_id', 'sched_id');
    }
}
