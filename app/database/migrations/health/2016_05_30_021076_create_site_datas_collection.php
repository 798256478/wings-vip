<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteDatasCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('site_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('record_id')->unsigned();
            $table->string('code',50);
            $table->string('genotype',20);
            $table->integer('singleType');
            $table->decimal('score')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('record_id')->references('id')->on('records');
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
         Schema::drop('site_datas');
    }
}
