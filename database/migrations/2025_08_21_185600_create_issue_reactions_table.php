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
    Schema::create('issue_reactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('issue_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
        $table->enum('reaction_type', ['support', 'affected', 'not_sure', 'invalid', 'fixed']);
        $table->timestamps();

        $table->unique(['issue_id', 'user_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reactions');
    }
};
