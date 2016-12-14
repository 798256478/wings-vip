<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_templates', function (Blueprint $table) {

            $table->increments('id');
            $table->string('wechat_ticket_id', 32)->unique();
            $table->string('title', 27);
            $table->string('sub_title', 54)->nullable();
            $table->string('color', 16);
            $table->string('notice', 48);
            $table->string('description', 3072);
            $table->integer('quantity');
            $table->integer('get_limit')->nullable();
            $table->string('location_id_list')->nullable();
            $table->enum('date_info_type', [
                'DATE_TYPE_FIX_TERM',
                'DATE_TYPE_FIX_TIME_RANGE']
                );
            $table->timestamp('begin_timestamp');
            $table->timestamp('end_timestamp');
            $table->enum('card_type', [
                'GENERAL_COUPON',
                'GROUPON',
                'GIFT',
                'DISCOUNT',
                'CASH'
                ]);
            $table->string('groupon_deal_detail')->nullable();
            $table->integer('cash_least_cost')->nullable();
            $table->integer('cash_reduce_cost')->nullable();
            $table->integer('discount_discount')->nullable();
            $table->string('gift_gift', 3072)->nullable();
            $table->string('general_coupon_default_detail', 3072)->nullable();
            $table->enum('wechat_status', [
                'CARD_STATUS_USER_DISPATCH',
                'CARD_STATUS_USER_DELETE',
                'CARD_STATUS_VERIFY_OK',
                'CARD_STATUS_VERIFY_FAIL',
                'CARD_STATUS_NOT_VERIFY'
                ]);
            $table->boolean('disable_online')->default(false);
            $table->boolean('disable_shop')->default(false);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ticket_templates');
    }
}
