<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_sites', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('站点名称');
            $table->string('county')->comment('国家');
            $table->string('province')->comment('省/州');
            $table->string('province_simple')->comment('省/州 简写');
            $table->string('city')->comment('城市名');
            $table->string('city_simple')->comment('城市名 简写');

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
        Schema::dropIfExists('service_sites');
    }
}
