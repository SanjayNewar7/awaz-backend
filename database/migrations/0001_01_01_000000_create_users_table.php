<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username', 50)->unique();
    $table->string('first_name', 50);
    $table->string('last_name', 50);
    $table->string('email', 100)->unique();
    $table->string('phone_number', 10);
    $table->string('password');
    $table->string('district', 50);
    $table->string('city', 50);
    $table->integer('ward');
    $table->string('area_name', 100);
    $table->string('citizenship_id_number', 50)->unique();
    $table->enum('gender', ['Male', 'Female', 'Other']);
    $table->boolean('is_verified');
    $table->boolean('agreed_to_terms');
    $table->string('citizenship_front_image'); // For Base64 or path
    $table->string('citizenship_back_image');  // For Base64 or path
    $table->string('province', 50)->nullable();
    $table->timestamps();
});
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
