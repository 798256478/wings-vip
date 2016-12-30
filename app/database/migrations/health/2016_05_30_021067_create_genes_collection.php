<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenesCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('genes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('default_is_positive');
            $table->integer('site_count');
            $table->string('default_effect', 50);
            $table->timestamps();
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
         Schema::drop('genes');
    }
}
