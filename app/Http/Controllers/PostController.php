<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
