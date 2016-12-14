<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('order_detail_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned();//订单id
            
            $table->enum('type', [
                'ORDER',//整单退
                'GOODS',//商品退
            ]);//退款方式
            
            $table->enum('state', [
                'APPLY',//申请
                'REFUND',//同意
                'REFUSED',//拒绝
                'CLOSE',//关闭
            ]);
            
            $table->string('name',20);
            $table->string('phone',20);
            $table->integer('reason');//商品运送途中已损毁,商品不符,订单有误,故障, 请详述,其它, 请详述
            $table->timestamp('apply_time')->nullable();
            
            $table->decimal('money')->default(0);
            $table->boolean('is_refund_other')->default(0);
            $table->string('instructions',200)->nullable();
            $table->timestamp('hand_time')->nullable();
            
            $table->timestamp('close_time')->nullable();
            $table->timestamps();
            $table->foreign('order_detail_id')->references('id')->on('order_details');
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
        Schema::drop('refunds');
    }
}
