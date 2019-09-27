<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecyclerWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycler_withdraws', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('recycler_id')->comment('recycler_id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->string('type')->comment('提现申请类型: 银联,微信,支付宝');
            $table->string('status')->comment('提现状态: 待审核,已通过,已拒绝');

            $table->string('money')->comment('金额');

            $table->json('info')->comment('提现预留信息');

            $table->string('reason')->nullable()->comment('回复拒绝原因等信息');

            $table->timestamp('checked_at')->nullable()->comment('审核时间');
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
        Schema::dropIfExists('recycler_withdraws');
    }
}
