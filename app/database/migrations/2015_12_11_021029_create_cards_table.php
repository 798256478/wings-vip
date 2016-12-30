<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {

            $table->increments('id');
            $table->string('card_code', 20)->unique();
            $table->string('openid', 32)->unique();
            $table->string('password', 60);//裕达用
            $table->string('nickname', 20);
            $table->integer('sex');
            $table->string('headimgurl', 1024)->nullable();
            $table->integer('level')->default(0);
            $table->integer('bonus')->default(0);
            $table->decimal('balance')->default(0);
            $table->string('mobile', 15)->nullable();
            $table->string('pin', 15)->nullable();
            $table->string('name', 20)->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('detail_location', 1024)->nullable();
            $table->string('habit')->nullable();
            $table->decimal('total_expense')->default(0);
            $table->integer('total_visit')->default(0);
            $table->integer('referrer_id')->nullable();
            $table->decimal('commission')->default(0);
            $table->boolean('is_wechat_received')->default(false);
            $table->integer('month_receive')->default(0);
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
        Schema::drop('cards');
    }
}
