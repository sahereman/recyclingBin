<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecyclerMoneyBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycler_money_bills', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('recycler_id')->comment('user-id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->string('type')->comment('账单类型 orderPayment | distributionIncome | orderRefund');

            $table->string('description')->default('')->comment('描述');

            $table->string('operator')->comment('运算符');
            $table->decimal('number')->comment('金额数目');

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
        Schema::dropIfExists('recycler_money_bills');
    }
}
