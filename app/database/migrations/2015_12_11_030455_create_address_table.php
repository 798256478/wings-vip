<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('card_id')->unsigned();
            $table->string('name');
            $table->string('tel',20);
            $table->string('province', 50);
            $table->string('city',50);
            $table->string('area',50);
            $table->string('detail',200);
            $table->boolean('isdefault')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('card_id')->references('id')->on('cards');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('address');
    }
}
