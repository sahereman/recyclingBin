<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBinRecyclersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('bin_recyclers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->comment('bin_id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');

            $table->unsignedInteger('recycler_id')->comment('recycler_id');
            $table->foreign('recycler_id')->references('id')->on('recyclers')->onDelete('cascade');

            $table->boolean('fabric_permission')->default(true)->comment('纺织物开箱权限');
            $table->boolean('paper_permission')->default(true)->comment('可回收物开箱权限');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bin_recyclers');
    }
}
