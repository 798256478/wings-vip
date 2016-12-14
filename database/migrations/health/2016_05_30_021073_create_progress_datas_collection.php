<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgressDatasCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('progress_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('experiment_data_id')->nullable()->unsigned();
            $table->integer('progress_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('experiment_data_id')->references('id')->on('experiment_datas');
            $table->foreign('progress_id')->references('id')->on('progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('progress_datas');
    }
}
