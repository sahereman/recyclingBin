<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoxOrdersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('box_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('box_id')->nullable()->comment('box-id');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('set null');

            $table->unsignedInteger('user_id')->comment('user-id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('sn')->comment('传统箱订单序列号');
            $table->string('status')->default('completed')->comment('order-status:completed[已完成]')->index();

            $table->string('image_proof')->comment('上传图片凭证');

            $table->unsignedDecimal('total')->comment('合计');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('box_orders');
    }
}
