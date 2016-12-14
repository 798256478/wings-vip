<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('experiment_id')->unsigned();
            $table->integer('sitecount')->default(0);
            $table->integer('order')->default(0);
            $table->integer('parent')->nullable();
            $table->string('method', 50);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('experiment_id')->references('id')->on('experiments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('projects');
    }
}
