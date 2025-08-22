<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddIssueIdToPostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('issue_id')->after('post_id')->nullable();
            $table->foreign('issue_id')->references('id')->on('issues')->onDelete('cascade');
        });

        // Map existing posts to their issues based on user_id, title, and description
        $posts = DB::table('posts')->get();
        foreach ($posts as $post) {
            $issue = DB::table('issues')
                ->where('user_id', $post->user_id)
                ->where('heading', $post->title)
                ->where('description', $post->description)
                ->first();
            if ($issue) {
                DB::table('posts')
                    ->where('post_id', $post->post_id)
                    ->update(['issue_id' => $issue->id]);
            } else {
                Log::warning("No matching issue found for post_id: {$post->post_id}, user_id: {$post->user_id}, title: {$post->title}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['issue_id']);
            $table->dropColumn('issue_id');
        });
    }
}
