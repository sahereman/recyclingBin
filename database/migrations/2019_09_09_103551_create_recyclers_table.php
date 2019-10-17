<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecyclersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recyclers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('昵称');
            $table->string('password');
            $table->string('phone')->comment('手机');
            $table->string('avatar')->nullable()->comment('头像');
            $table->unsignedDecimal('money')->default(0)->comment('余额');
            $table->unsignedDecimal('frozen_money')->default(0)->comment('冻结的余额,用于提现中金额');

            $table->unsignedInteger('notification_count')->default(0)->comment('通知未读数');
            $table->timestamp('disabled_at')->nullable()->comment('禁用时间');

            $table->timestamp('contract_start_time')->nullable()->comment('合约开始时间');
            $table->timestamp('contract_end_time')->nullable()->comment('合约结束时间');

            $table->string('wx_openid')->nullable()->comment('微信授权id');
            $table->string('wx_session_key')->nullable()->comment('微信会话秘钥');

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
        Schema::dropIfExists('recyclers');
    }
}
