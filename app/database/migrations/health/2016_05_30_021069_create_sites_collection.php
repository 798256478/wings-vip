<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesCollection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('sites', function (Blueprint $table) {
            $table->string('code', 50)->primary();
            $table->string('rs_code', 20)->unique();
            $table->integer('gene_id')->unsigned();
            $table->string('mutation', 20);
            $table->string('SNPSite', 50);
            $table->integer('DNASingleType');
            $table->enum('type',[
                'snp',      //snp
                'para'])->nullable(); //属性
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('gene_id')->references('id')->on('genes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('sites');
    }
}
