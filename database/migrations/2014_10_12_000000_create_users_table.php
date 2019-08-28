<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wx_openid')->nullable()->comment('微信授权id');
            $table->string('name')->comment('昵称');
            $table->string('gender')->default('')->comment('性别');
            $table->string('phone')->default('')->comment('手机');
            $table->string('avatar')->nullable()->comment('头像');
            $table->decimal('money')->default(0)->comment('奖励金');


            $table->string('wx_country')->default('')->comment('WX国家');
            $table->string('wx_province')->default('')->comment('WX省');
            $table->string('wx_city')->default('')->comment('WX市');


            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
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
        Schema::dropIfExists('users');
    }
}
