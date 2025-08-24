<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('liked_user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('liked_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'liked_user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
