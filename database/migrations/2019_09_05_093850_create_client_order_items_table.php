<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_order_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('order_id')->comment('order-id');
            $table->foreign('order_id')->references('id')->on('client_orders')->onDelete('cascade');

            $table->string('type_name')->comment('回收分类名称');

            $table->decimal('number')->comment('数量');
            $table->string('unit')->comment('计量单位');

            $table->string('subtotal')->comment('小计');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_order_items');
    }
}
