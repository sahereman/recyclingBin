<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOrderItemTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_order_item_temps', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->comment('bin-id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');

            $table->string('type_slug')->comment('回收分类标示');
            $table->string('type_name')->comment('回收分类名称');

            $table->unsignedDecimal('number')->comment('数量');
            $table->string('unit')->comment('计量单位');

            $table->unsignedDecimal('subtotal')->comment('小计');

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
        Schema::dropIfExists('client_order_item_temps');
    }
}
