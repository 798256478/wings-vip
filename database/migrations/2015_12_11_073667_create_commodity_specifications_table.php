<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditySpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_specifications', function (Blueprint $table) {
            $table->increments('id');//规格ID
            $table->integer('commodity_id')->unsigned();//商品ID
            $table->string('name');//规格名称
            $table->string('full_name')->nullable();//规格全名（含商品名）
            $table->decimal('price')->default(0);//价格
            $table->integer('bonus_require')->default(0);//所需积分
            $table->integer('stock_quantity')->default(0);//库存
            $table->string('sellable_type')->nullable();//销售物类型（服务、卡券、实物商品）
            $table->integer('sellable_id')->unsigned()->nullable();//销售物ID（服务、卡券）
            $table->integer('sellable_quantity')->nullable();//服务次数
            $table->integer('sellable_validity_days')->nullable();//服务有效期
            $table->boolean('is_suite')->default(false);//是否是套装
            $table->integer('is_on_offer')->default(true);//是否上架
            $table->boolean('is_need_delivery')->default(false);//是否需要配送

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('commodity_id')->references('id')->on('commodities');//所属商品
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commodity_specifications');
    }
}
