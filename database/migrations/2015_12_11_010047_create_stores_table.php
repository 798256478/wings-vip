<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function(Blueprint $table){
            $table->increments('id');
            $table->string('poi_id', 20)->unique();
            $table->string('business_name', 20);
            $table->string('barnch_name', 20);
            $table->string('province', 20);
            $table->string('city', 20);
            $table->string('district', 20);
            $table->string('address', 100);
            $table->string('telephone', 20);
            $table->string('categories', 20);
            $table->double('longitude', 14, 10);
            $table->double('latitude', 14, 10);
            $table->string('photo_list');
            $table->string('special', 200);
            $table->string('open_time', 20);
            $table->integer('avg_price');
            $table->string('introduction', 500)->nullable();
            $table->string('recommend', 500)->nullable();

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
        Schema::drop('stores');
    }
}
