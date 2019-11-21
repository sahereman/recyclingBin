<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoxAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('box_admin_users', function (Blueprint $table) {
            $table->increments('id');
//
            $table->unsignedInteger('box_id')->comment('box_id');
            $table->foreign('box_id')->references('id')->on('boxes')->onDelete('cascade');

            $table->unsignedInteger('admin_user_id')->comment('admin_user_id');
//            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
//
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('box_admin_users');
    }
}
