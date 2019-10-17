<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCleanOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clean_order_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('order_id')->comment('order-id');
            $table->foreign('order_id')->references('id')->on('clean_orders')->onDelete('cascade');

            $table->string('type_slug')->comment('回收分类标示');
            $table->string('type_name')->comment('回收分类名称');

            $table->unsignedDecimal('number')->comment('数量');
            $table->string('unit')->comment('计量单位');

            $table->unsignedDecimal('subtotal')->comment('小计');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clean_order_items');
    }
}
