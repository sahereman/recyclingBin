<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCleanOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clean_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->nullable()->comment('bin-id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('set null');

            $table->unsignedInteger('recycler_id')->comment('recycler_id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->string('sn')->comment('回收订单序列号');
            $table->string('status')->default('completed')->comment('order-status:completed[已完成]')->index();

            $table->json('bin_snapshot')->comment('回收箱快照');

            $table->decimal('total')->comment('合计');

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
        Schema::dropIfExists('clean_orders');
    }
}
