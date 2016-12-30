<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentDatasCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('experiment_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('barcode_id')->unsigned();
            $table->integer('experiment_id')->unsigned();
            $table->integer('progress_id')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('experiment_id')->references('id')->on('experiments');
            $table->foreign('barcode_id')->references('id')->on('barcodes');
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
         Schema::drop('experiment_datas');
    }
}
