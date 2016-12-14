<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->string('name',50);
            $table->enum('type', [
                'WECHAT',
                'BALANCE',
                'CASHIER'
            ]);
            $table->decimal('amount');
            $table->integer('use_bonus')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_payments');
    }
}
