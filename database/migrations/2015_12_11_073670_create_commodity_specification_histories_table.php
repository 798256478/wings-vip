<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditySpecificationHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodity_specification_histories', function (Blueprint $table) {

            $table->increments('id');//规格历史版本ID
            $table->integer('commodity_history_id')->unsigned();//所属商品历史版本ID
            $table->integer('commodity_specification_id')->unsigned();//所属商品规格ID
            $table->string('name');//规格名称
            $table->string('full_name')->nullable();//规格全名（含商品名）
            $table->decimal('price')->default(0);//价格
            $table->integer('bonus_require')->default(0);//所需积分
            $table->string('sellable_type')->nullable();//销售物类型（服务、卡券、实物商品）
            $table->integer('sellable_id')->unsigned()->nullable();//销售物ID（服务、卡券）
            $table->integer('sellable_quantity')->nullable();//服务次数
            $table->integer('sellable_validity_days')->nullable();//服务有效期
            $table->boolean('is_suite')->default(false);//是否是套装
            $table->boolean('is_need_delivery')->default(false);//是否需要配送
            $table->timestamps();

            $table->foreign('commodity_history_id')->references('id')->on('commodity_histories');
            $table->foreign('commodity_specification_id','csh_cs_foreign')->references('id')->on('commodity_specifications');//关系名称太长
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commodity_specification_histories');
    }
}
