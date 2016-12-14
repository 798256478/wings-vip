<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('card_id')->nullable();
            // $table->string('good_code',50)->nullable();
            $table->integer('barcode_id')->nullable()->unsigned();
            $table->string('userNO',50)->nullable();
            $table->string('name',50)->nullable();
            $table->string('address',50)->nullable();
            $table->string('postalCode',50)->nullable();
            $table->integer('sex')->nullable();//0->女;1->男
            $table->string('mailbox',50)->nullable();
            $table->integer('age')->nullable();
            $table->string('Nation',50)->nullable();
            $table->integer('Role')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('mobile',50)->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('barcode_id')->references('id')->on('barcodes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('customers');
    }
}
