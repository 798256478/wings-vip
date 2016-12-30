<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('experiment_data_id')->unsigned();
            $table->string('sampleNo', 50);
            $table->timestamp('time');
            $table->string('inspector');
            $table->string('assessor');
          
            $table->timestamps();
            $table->softDeletes();

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
         Schema::drop('records');
    }
}
