<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('card_id')->unsigned();
            $table->integer('property_template_id')->unsigned();
            $table->timestamp('expiry_date')->nullable();
            $table->integer('quantity')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('card_id')->references('id')->on('cards');
            $table->foreign('property_template_id')->references('id')->on('property_templates');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('properties');
    }
}
