<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user:user_id,username,profile_image')
            ->orderBy('created_at', 'desc')
            ->get();
        Log::info('Fetched posts: ', ['count' => $posts->count(), 'posts' => $posts->toArray()]);
        $posts = $posts->map(function ($post) {
            $profileImage = $post->user->profile_image;
            $profileImage = $profileImage ? str_replace('public/', '', $profileImage) : null;

            return [
                'post_id' => $post->post_id,
                'issue_id' => $post->issue_id, // Add this
                'user_id' => $post->user_id,
                'username' => $post->user->username,
                'profile_image' => $profileImage,
                'title' => $post->title,
                'description' => $post->description,
                'category' => $post->category,
                'image1' => $post->image1,
                'image2' => $post->image2,
                'support_count' => $post->support_count,
                'affected_count' => $post->affected_count,
                'not_sure_count' => $post->not_sure_count,
                'invalid_count' => $post->invalid_count,
                'fixed_count' => $post->fixed_count,
                'comment_count' => $post->comment_count,
                'created_at' => $post->getFormattedCreatedAt(),
                'updated_at' => $post->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ]);
    }
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post);
    }

     public function getByIssueId($issue_id)
    {
        try {
            $post = Post::where('issue_id', $issue_id)
                ->with('user:user_id,username,profile_image')
                ->first();

            if (!$post) {
                Log::warning("No post found for issue_id: {$issue_id}");
                return response()->json(['error' => 'Post not found'], 404);
            }

            $postData = [
                'post_id' => $post->post_id,
                'issue_id' => $post->issue_id,
                'user_id' => $post->user_id,
                'username' => $post->user ? $post->user->username : 'Unknown',
                'profile_image' => $post->user && $post->user->profile_image ? str_replace('public/', '', $post->user->profile_image) : null,
                'title' => $post->title,
                'description' => $post->description,
                'category' => $post->category,
                'image1' => $post->image1,
                'image2' => $post->image2,
                'support_count' => $post->support_count,
                'affected_count' => $post->affected_count,
                'not_sure_count' => $post->not_sure_count,
                'invalid_count' => $post->invalid_count,
                'fixed_count' => $post->fixed_count,
                'comment_count' => $post->comment_count,
                'created_at' => $post->created_at ? Carbon::parse($post->created_at)->toDateTimeString() : null,
                'updated_at' => $post->updated_at ? Carbon::parse($post->updated_at)->toDateTimeString() : null,
            ];

            return response()->json($postData);
        } catch (\Exception $e) {
            Log::error("Error in getByIssueId for issue_id {$issue_id}: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
