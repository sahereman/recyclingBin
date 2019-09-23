<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBinTokensTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('bin_tokens', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->comment('bin_id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');

            $table->string('token');
            $table->unsignedInteger('fd');

            $table->string('related_model')->nullable()->default(null)->comment('关联模型');
            $table->unsignedInteger('related_id')->nullable()->default(null)->comment('关联模型ID');

            $table->string('auth_model')->nullable()->default(null)->comment('认证模型');
            $table->unsignedInteger('auth_id')->nullable()->default(null)->comment('认证模型ID');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bin_tokens');
    }
}
