<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('experiment_data_id')->unsigned();
            $table->string('name',50);
            $table->string('phone',50);
            $table->integer('sex');//0->女;1->男
            $table->timestamp('time');

            $table->foreign('experiment_data_id')->references('id')->on('experiment_datas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('reservations');
    }
}
