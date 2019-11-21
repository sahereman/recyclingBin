<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('site_id')->comment('站点id');
            $table->foreign('site_id')->references('id')->on('service_sites')->onDelete('cascade');

            $table->string('status')->comment('满箱状态');
            $table->string('name')->comment('名称');
            $table->string('no')->unique()->comment('设备编号');
            $table->string('lat')->comment('纬度');
            $table->string('lng')->comment('经度');
            $table->string('address')->comment('地址');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boxes');
    }
}
