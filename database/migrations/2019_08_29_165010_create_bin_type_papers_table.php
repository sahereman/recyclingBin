<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBinTypePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bin_type_papers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('bin_id')->comment('所属箱');
            $table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');

            $table->string('name')->comment('种类名称');
            $table->string('status')->comment('状态');

            $table->unsignedDecimal('number')->comment('数量');
            $table->string('unit')->comment('计量单位');

            $table->unsignedDecimal('threshold')->default(0)->comment('阈值');

            $table->unsignedDecimal('real_weight')->default(0)->comment('实时重量(回收机传输)');

            $table->unsignedInteger('client_price_id')->comment('客户端价格');
            $table->foreign('client_price_id')->references('id')->on('client_prices')->onDelete('cascade');

            $table->unsignedInteger('clean_price_id')->comment('回收端价格');
            $table->foreign('clean_price_id')->references('id')->on('clean_prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bin_type_papers');
    }
}
