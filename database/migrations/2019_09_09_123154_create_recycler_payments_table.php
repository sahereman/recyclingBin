<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecyclerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycler_payments', function (Blueprint $table) {
            $table->increments('id');

            $table->string('sn')->comment('sn');
            $table->unique('sn');

            $table->unsignedInteger('recycler_id')->comment('recycler_id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->string('related_model')->nullable()->comment('关联模型');

            $table->unsignedDecimal('amount')->comment('amount:支付金额');
            $table->string('method')->nullable()->comment('method:wechat');
            $table->string('payment_sn')->nullable()->comment('payment-sn');
            $table->timestamp('paid_at')->nullable()->comment('成功支付时间');

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
        Schema::dropIfExists('recycler_payments');
    }
}
