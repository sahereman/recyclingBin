<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCleanPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clean_prices', function (Blueprint $table) {
            $table->increments('id');

            $table->string('slug')->unique()->comment('标示位');
            $table->unsignedDecimal('price')->comment('回收端价格');
            $table->string('unit')->comment('计量单位');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clean_prices');
    }
}
