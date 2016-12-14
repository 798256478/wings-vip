<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSyncFailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sync_fail', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('card_id')->unsigned();
            $table->timestamp('time')->nullable();
            $table->integer('http_code')->nullable();
            $table->char('error_code',100)->nullable();
            $table->text('back_data')->nullable();
            $table->text('send_data')->nullable();
            $table->enum('state', [
                'FAIL',//未解决
                'SUCCEED',//已解决
            ]);
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('card_id')->references('id')->on('cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_sync_fail');
    }
}
