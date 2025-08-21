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
    Schema::table('issues', function (Blueprint $table) {
        $table->integer('support_count')->default(0);
        $table->integer('affected_count')->default(0);
        $table->integer('not_sure_count')->default(0);
        $table->integer('invalid_count')->default(0);
        $table->integer('fixed_count')->default(0);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            //
        });
    }
};
