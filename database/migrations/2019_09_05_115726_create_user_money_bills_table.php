<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMoneyBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_money_bills', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('user-id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('type')->comment('账单类型 clientOrder | userWithdraw');

            $table->string('description')->default('')->comment('描述');

            $table->string('operator')->comment('运算符');
            $table->unsignedDecimal('number')->comment('金额数目');

            $table->string('related_model')->nullable()->default(null)->comment('关联模型');
            $table->unsignedInteger('related_id')->nullable()->default(null)->comment('关联模型ID');

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
        Schema::dropIfExists('user_money_bills');
    }
}
