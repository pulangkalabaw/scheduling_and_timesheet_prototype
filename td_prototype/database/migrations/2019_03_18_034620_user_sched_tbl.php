<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserSchedTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_sched_tbl', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('user_id');
            $table->integer('sched_id');
            $table->string('starts_at');
            $table->integer('late_monitor')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('user_sched_tbl');

    }
}
