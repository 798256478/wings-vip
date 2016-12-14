<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditySuiteChildHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_suite_child_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('suite_history_id')->unsigned();//子套装id
            $table->integer('child_history_id')->unsigned();//子商品规格id
            $table->integer('count')->default(0);

            $table->foreign('suite_history_id')->references('id')->on('commodity_specification_histories');
            $table->foreign('child_history_id')->references('id')->on('commodity_specification_histories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commodity_suite_child_histories');
    }
}
