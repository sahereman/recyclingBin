<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecyclerDepositsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('recycler_deposits', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('recycler_id')->comment('recycler_id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->string('sn')->comment('充值单号');
            $table->string('status')->comment('状态: paying|completed');
            $table->string('method')->comment('充值方式: wechat');
            $table->unsignedDecimal('money')->comment('充值金额');

            $table->string('payment_sn')->nullable()->comment('支付单号');
            $table->timestamp('paid_at')->nullable()->comment('支付时间');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recycler_deposits');
    }
}
