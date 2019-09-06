<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdraws', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('user-id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('type')->comment('提现申请类型: 银联,微信,支付宝');
            $table->string('status')->comment('提现状态: 待审核,已通过,已拒绝');

            $table->string('money')->comment('金额');

            $table->json('info')->comment('提现预留信息');

            $table->string('reason')->nullable()->comment('回复拒绝原因等信息');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_withdraws');
    }
}
