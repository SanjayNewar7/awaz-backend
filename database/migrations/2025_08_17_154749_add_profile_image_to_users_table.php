<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileImageToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'profile_image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->longText('profile_image')->nullable()->after('bio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_image');
        });
    }
}
