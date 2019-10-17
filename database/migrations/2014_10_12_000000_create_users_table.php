<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('昵称');
            $table->string('gender')->default('')->comment('性别');
            $table->string('phone')->default('')->comment('手机');
            $table->string('avatar')->nullable()->comment('头像');

            $table->unsignedDecimal('money')->default(0)->comment('奖励金');
            $table->unsignedDecimal('frozen_money')->default(0)->comment('冻结的奖励金,用于提现中金额');

            $table->unsignedDecimal('total_client_order_money')->default(0)->comment('累计投递订单金额');
            $table->unsignedInteger('total_client_order_count')->default(0)->comment('累计投递订单次数');
            $table->unsignedDecimal('total_client_order_number')->default(0)->comment('累计投递订单重量');

            $table->string('wx_openid')->nullable()->comment('微信授权id');
            $table->string('wx_session_key')->nullable()->comment('微信会话秘钥');
            $table->string('wx_country')->default('')->comment('WX国家');
            $table->string('wx_province')->default('')->comment('WX省');
            $table->string('wx_city')->default('')->comment('WX市');

            $table->string('real_id')->default('')->comment('身份证号');
            $table->string('real_name')->default('')->comment('真实姓名');
            $table->timestamp('real_authenticated_at')->nullable()->comment('实名认证时间');

            $table->unsignedInteger('notification_count')->default(0)->comment('通知未读数');

            $table->timestamp('disabled_at')->nullable()->comment('禁用时间');

            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
