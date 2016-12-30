<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {

            $table->increments('id');
            $table->string('ticket_code', 15)->unique();
            $table->integer('card_id')->unsigned();
            $table->integer('ticket_template_id')->unsigned();
            $table->boolean('is_wechat_received')->default(false);
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('card_id')->references('id')->on('cards');
            $table->foreign('ticket_template_id')->references('id')->on('ticket_templates');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tickets');
    }
}
