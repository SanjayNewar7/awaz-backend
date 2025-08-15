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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned(false);
            $table->string('heading');
            $table->text('description');
            $table->string('report_type');
            $table->string('district');
            $table->string('ward');
            $table->string('area_name');
            $table->string('location');
            $table->text('photo1')->nullable();
            $table->text('photo2')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
