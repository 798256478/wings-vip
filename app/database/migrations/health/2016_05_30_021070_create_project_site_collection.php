<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSiteCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('project_site', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50);
            $table->integer('project_id')->unsigned();
            $table->text('weight');
            $table->integer('isPositive');
            $table->string('mutation',20);
           
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('code')->references('code')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('project_site');
    }
}
