<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('commodity_specification_id')->unsigned();
            $table->integer('commodity_specification_history_id')->unsigned();
            $table->decimal('unit_price')->default(0);
            $table->integer('unit_bonus_require')->default(0);
            $table->integer('amount')->default(1);
            $table->decimal('total_price')->default(0);
            $table->integer('total_bonus_require')->default(0);
            $table->boolean('is_refund')->default(false);
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('commodity_specification_id')->references('id')->on('commodity_specifications');
            $table->foreign('commodity_specification_history_id')->references('id')->on('commodity_specification_histories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_details');
    }
}
