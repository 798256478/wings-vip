<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signs', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('card_id')->unsigned();
            $table->integer('bonus');
            $table->integer('days');
            $table->timestamps();
            
            $table->foreign('card_id')->references('id')->on('cards');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('signs');
    }
}
