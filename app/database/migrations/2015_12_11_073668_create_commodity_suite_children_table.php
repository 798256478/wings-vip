<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditySuiteChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_suite_children', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('suite_id')->unsigned();//子套装id
            $table->integer('child_id')->unsigned();//子商品规格id
            $table->integer('count')->default(0);//子商品数量

            $table->foreign('suite_id')->references('id')->on('commodity_specifications');
            $table->foreign('child_id')->references('id')->on('commodity_specifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commodity_suite_children');
    }
}
