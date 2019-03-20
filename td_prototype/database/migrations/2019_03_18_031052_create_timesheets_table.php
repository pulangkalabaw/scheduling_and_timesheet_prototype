<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('td_in')->nullable();
            $table->string('td_out')->nullable();
			$table->integer('sched_id')->unsigned();
            $table->integer('late')->unsigned()->nullable();
            $table->integer('undertime')->unsigned()->nullable();
            $table->integer('overtime')->unsigned()->nullable();
            $table->integer('worked_mins')->unsigned()->nullable();
            $table->integer('status')->unsigned()->nullable();;
            $table->timestamps();
        });

        Schema::table('timesheets', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheets');
    }
}
