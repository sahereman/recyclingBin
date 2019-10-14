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

            $table->unsignedInteger('payment_id')->comment('payment_id');
            $table->foreign('payment_id')->references('id')->on('recycler_payments')->onDelete('cascade');

            $table->string('status')->comment('状态: paying|completed');
            $table->unsignedDecimal('money')->comment('充值金额');


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
