<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBinWeightWarningsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('bin_weight_warnings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->comment('bin_id');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');

            $table->string('type_slug')->comment('回收分类标示');
            $table->string('type_name')->comment('回收分类名称');

            $table->decimal('normal_weight')->comment('正确数量');
            $table->decimal('measure_weight')->comment('测量数量');

            $table->decimal('exception_weight')->comment('异常数量');
            $table->string('unit')->comment('计量单位');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bin_weight_warnings');
    }
}
