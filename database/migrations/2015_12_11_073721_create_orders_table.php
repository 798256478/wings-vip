<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->increments('id');
            $table->string('guid',150)->unique();//裕达用的主键
            $table->integer('card_id')->unsigned();
            $table->string('number', 32)->index();
            $table->string('body', 32);
            $table->text('remark');
            $table->enum('type',[
               'CONSUME',
               'BALANCE',
               'GOODS',
            ]);
            $table->enum('channel',[
                'SHOP',
                'WECHAT',
                'DELIVERY'
            ]);
            
            $table->decimal('balance_fee')->default(0);//储值金额
            $table->integer('balance_present')->default(0);//储值赠送金额
            
            $table->decimal('total_fee')->default(0);//订单总金额（未折扣）
            $table->integer('bonus_require')->default(0);//所需积分（未修改）
            
            $table->decimal('money_pay_amount')->default(0);//实付金额（折扣后，实际支付）
            $table->integer('bonus_pay_amount')->default(0);//积分支付（修改后，实际支付）
            $table->integer('bonus_present')->default(0);//赠送积分
            
            $table->enum('state', [
                'NOT_PAY',//等待买家付款
                'PAY_SUCCESS',//已付款
                'DELIVERED',//已发货
                'FINISH',//交易成功
                'CLOSED',//关闭
            ]);
            $table->string('address', 300)->nullable();
            $table->enum('express_type',[
                'SELF',      //自己派送
                'EXPRESS'])->nullable(); //快递
            $table->string('express_code', 300)->nullable();
            $table->string('express_company',30)->nullable();
            $table->boolean('is_need_delivery')->default(false);
            $table->timestamp('pay_time')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->timestamp('finish_time')->nullable();
            $table->timestamp('close_time')->nullable();
            $table->integer('ticket_id')->nullable();
            
            // $table->integer('suit_id')->nullable();
            // $table->integer('suit_amount')->nullable();
            $table->integer('cashier_id')->nullable();
            $table->decimal('cashier_price_deductions')->default(0);//收银员减免金额
            $table->integer('cashier_bonus_deductions')->default(0);//收银员减免积分
            $table->timestamps();
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
        Schema::drop('orders');
    }
}
