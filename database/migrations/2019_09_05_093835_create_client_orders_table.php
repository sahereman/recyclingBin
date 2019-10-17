<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->nullable()->comment('bin-id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('set null');

            $table->unsignedInteger('user_id')->comment('user-id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('sn')->comment('投递订单序列号');
            $table->string('status')->default('completed')->comment('order-status:completed[已完成]')->index();

            $table->json('bin_snapshot')->comment('回收箱快照');

            $table->unsignedDecimal('total')->comment('合计');

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
        Schema::dropIfExists('client_orders');
    }
}
