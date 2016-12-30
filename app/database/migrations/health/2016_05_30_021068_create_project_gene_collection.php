<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectGeneCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('project_gene', function (Blueprint $table) {
            // $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('gene_id')->unsigned();
            $table->string('effect', 50);
            
            $table->timestamps();
            $table->softDeletes();
            $table->primary(array('project_id', 'gene_id')); 
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('gene_id')->references('id')->on('genes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('project_gene');
    }
}
