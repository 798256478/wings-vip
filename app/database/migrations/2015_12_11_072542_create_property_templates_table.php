<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_templates', function (Blueprint $table) {

            $table->increments('id');
            $table->string('title', 27);
            $table->string('color', 16);
            $table->string('icon', 20)->default('');
            $table->string('image_icon', 100)->default('');
            $table->string('notice', 48);
            $table->string('description', 3072);

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
        Schema::drop('property_templates');
    }
}
