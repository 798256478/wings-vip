<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentsCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('experiments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('sampleType');
            $table->enum('type',[
                    'score',      //snp
                    'mean'])->nullable(); //属性
            // $table->string('url', 50);
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
         Schema::drop('experiments');
    }
}
