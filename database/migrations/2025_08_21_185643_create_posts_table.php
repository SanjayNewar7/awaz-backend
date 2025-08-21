<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id('post_id');
        $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
        $table->string('username');
        $table->string('title');
        $table->text('description');
        $table->string('category');
        $table->string('image1')->nullable();
        $table->string('image2')->nullable();
        $table->integer('support_count')->default(0);
        $table->integer('affected_count')->default(0);
        $table->integer('not_sure_count')->default(0);
        $table->integer('invalid_count')->default(0);
        $table->integer('fixed_count')->default(0);
        $table->integer('comment_count')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
