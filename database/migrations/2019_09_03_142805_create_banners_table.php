<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');

            $table->string('slug')->comment('标示位');
            $table->string('image')->comment('image-path');
            $table->string('link')->default('')->comment('链接地址');

            $table->unsignedSmallInteger('sort')->default(10)->comment('排序值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
