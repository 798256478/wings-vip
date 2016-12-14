<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities', function (Blueprint $table) {

            $table->increments('id');//商品ID
            $table->string('name');//商品全名
            $table->string('summary');//商品简述
            $table->string('code');//商品编码
            $table->string('image');//商品图片
            $table->text('detail');//商品详情
            $table->decimal('price')->default(0);//价格（最低价格）
            $table->integer('bonus_require')->default(0);//所需积分（最低所需积分）
            $table->boolean('is_single_specification')->default(false);//是否单规格商品
            $table->boolean('is_on_offer')->default(true);//是否上架
            $table->boolean('disable_coupon')->default(false);//是否禁用优惠
            $table->integer('quota_number')->default(0);//限购数量
            $table->integer('commodity_category_id')->unsigned();//商品分类ID
            $table->decimal('commission')->nullable();//返佣比例

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('commodity_category_id')->references('id')->on('commodity_categories');//所属分类
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commodities');
    }
}
