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
            $table->string('phone')->comment('手机');
            $table->string('avatar')->nullable()->comment('头像');
            $table->decimal('money')->default(0)->comment('余额');
            $table->decimal('frozen_money')->default(0)->comment('冻结的余额,用于提现中金额');

            $table->string('password');

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
