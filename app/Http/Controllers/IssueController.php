<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    /**
     * Save base64 image to storage (copied from AuthController for simplicity)
     */
    private function saveBase64Image($base64Image, $pathPrefix)
    {
        try {
            if (empty($base64Image)) {
                throw new \Exception('Empty image data');
            }

            if (strpos($base64Image, ';base64,') !== false) {
                list(, $base64Image) = explode(';', $base64Image);
                list(, $base64Image) = explode(',', $base64Image);
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            if (@imagecreatefromstring($imageData) === false) {
                throw new \Exception('Invalid image format');
            }

            $filename = $pathPrefix . uniqid() . '.jpg';
            $storagePath = 'public/images/' . $filename;

            if (!Storage::put($storagePath, $imageData)) {
                throw new \Exception('Failed to save image to storage');
            }

            return 'images/' . $filename;
        } catch (\Exception $e) {
            Log::error('Image processing error: ' . $e->getMessage());
            throw $e;
        }
    }
// In IssueController.php
public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'heading' => 'required|string|max:255',
            'description' => 'required|string',
            'report_type' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:50',
            'area_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'photo1' => 'nullable|string',
            'photo2' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $photo1Path = null;
        $photo2Path = null;

        if ($request->photo1) {
            $photo1Path = $this->saveBase64Image($request->photo1, 'issues/photo1_');
        }

        if ($request->photo2) {
            $photo2Path = $this->saveBase64Image($request->photo2, 'issues/photo2_');
        }

        $issue = Issue::create([
            'user_id' => auth()->id(),
            'heading' => $request->heading,
            'description' => $request->description,
            'report_type' => $request->report_type,
            'district' => $request->district,
            'ward' => $request->ward,
            'area_name' => $request->area_name,
            'location' => $request->location,
            'photo1' => $photo1Path,
            'photo2' => $photo2Path,
        ]);

        $user = auth()->user();
        \App\Models\Post::create([ // Import Post model
            'issue_id' => $issue->id,
            'user_id' => $user->user_id,
            'username' => $user->username,
            'title' => $request->heading,
            'description' => $request->description,
            'category' => $request->report_type,
            'image1' => $photo1Path,
            'image2' => $photo2Path,
            'support_count' => 0,
            'affected_count' => 0,
            'not_sure_count' => 0,
            'invalid_count' => 0,
            'fixed_count' => 0,
            'comment_count' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Issue created successfully',
            'issue' => $issue
        ], 201);
    } catch (\Exception $e) {
        Log::error('Issue creation error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Issue creation failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function index(Request $request)
    {
        try {
            $issues = Issue::with(['user', 'reactions', 'comments'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'issues' => $issues
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch issues: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch issues'
            ], 500);
        }
    }

    /**
     * Add reaction to an issue
     */
    public function addReaction(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reaction_type' => 'required|in:support,affected,not_sure,invalid,fixed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $issue = Issue::findOrFail($id);
            $userId = auth()->id();
            $reactionType = $request->reaction_type;

            // Check if user already reacted to this issue
            $existingReaction = DB::table('issue_reactions')
                ->where('issue_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($existingReaction) {
                // Update existing reaction
                DB::table('issue_reactions')
                    ->where('issue_id', $id)
                    ->where('user_id', $userId)
                    ->update(['reaction_type' => $reactionType]);
            } else {
                // Create new reaction
                DB::table('issue_reactions')->insert([
                    'issue_id' => $id,
                    'user_id' => $userId,
                    'reaction_type' => $reactionType,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Update post counts
            $this->updatePostReactionCounts($id);

            // Get updated reaction counts
            $reactionCounts = DB::table('issue_reactions')
                ->where('issue_id', $id)
                ->select('reaction_type', DB::raw('count(*) as count'))
                ->groupBy('reaction_type')
                ->get()
                ->keyBy('reaction_type');

            return response()->json([
                'status' => 'success',
                'message' => 'Reaction added successfully',
                'reaction_counts' => $reactionCounts
            ]);
        } catch (\Exception $e) {
            Log::error('Reaction error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add reaction'
            ], 500);
        }
    }

    /**
     * Update post reaction counts
     */
    private function updatePostReactionCounts($issueId)
    {
        $reactionCounts = DB::table('issue_reactions')
            ->where('issue_id', $issueId)
            ->select('reaction_type', DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->get()
            ->keyBy('reaction_type');

        $post = Post::where('issue_id', $issueId)->first();
        if ($post) {
            $post->update([
                'support_count' => $reactionCounts['support']->count ?? 0,
                'affected_count' => $reactionCounts['affected']->count ?? 0,
                'not_sure_count' => $reactionCounts['not_sure']->count ?? 0,
                'invalid_count' => $reactionCounts['invalid']->count ?? 0,
                'fixed_count' => $reactionCounts['fixed']->count ?? 0,
            ]);
        }
    }

    /**
     * Add comment to an issue
     */
    public function addComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string',
                'image' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $issue = Issue::findOrFail($id);
            $userId = auth()->id();

            // Save image if provided
            $imagePath = null;
            if ($request->image) {
                $imagePath = $this->saveBase64Image($request->image, 'comments/');
            }

            // Create comment
            $commentId = DB::table('issue_comments')->insertGetId([
                'issue_id' => $id,
                'user_id' => $userId,
                'comment' => $request->comment,
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update comment count in posts table
            $post = Post::where('issue_id', $id)->first();
            if ($post) {
                $post->increment('comment_count');
            }

            // Get the created comment with user details
            $comment = DB::table('issue_comments')
                ->join('users', 'issue_comments.user_id', '=', 'users.user_id')
                ->where('issue_comments.id', $commentId)
                ->select('issue_comments.*', 'users.first_name', 'users.last_name', 'users.profile_image')
                ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            Log::error('Comment error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add comment'
            ], 500);
        }
    }

    /**
     * Get comments for an issue
     */
    public function getComments($id)
    {
        try {
            $comments = DB::table('issue_comments')
                ->join('users', 'issue_comments.user_id', '=', 'users.user_id')
                ->where('issue_comments.issue_id', $id)
                ->select('issue_comments.*', 'users.first_name', 'users.last_name', 'users.profile_image')
                ->orderBy('issue_comments.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'comments' => $comments
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch comments: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch comments'
            ], 500);
        }
    }

}
