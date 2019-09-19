<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('category_id')->comment('所属分类');
            $table->foreign('category_id')->references('id')->on('topic_categories')->onDelete('cascade');

            $table->string('title')->comment('名称');
            $table->string('thumb')->nullable()->comment('缩略图');
            $table->string('image')->nullable()->comment('图片');
            $table->text('content')->nullable()->comment('内容 HTML');
            $table->boolean('is_index')->default(false)->comment('是否推荐');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topics');
    }
}
