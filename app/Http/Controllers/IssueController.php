<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Post;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    private function saveBase64Image($base64Image, $pathPrefix)
    {
        try {
            if (empty($base64Image)) {
                throw new \Exception('Empty image data');
            }

            if (strpos($base64Image, ';base64,') !== false) {
                list($meta, $base64Image) = explode(';', $base64Image);
                $extension = strpos($meta, 'image/png') !== false ? 'png' : 'jpg';
                list(, $base64Image) = explode(',', $base64Image);
            } else {
                $extension = 'jpg';
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new \Exception('Invalid base64 image data');
            }

            if (@imagecreatefromstring($imageData) === false) {
                throw new \Exception('Invalid image format');
            }

            $filename = $pathPrefix . uniqid() . '.' . $extension;
            $storagePath = 'images/' . $filename;

            if (!Storage::disk('public')->put($storagePath, $imageData)) {
                throw new \Exception('Failed to save image to storage');
            }

            return $storagePath;
        } catch (\Exception $e) {
            Log::error('Image processing error: ' . $e->getMessage());
            throw $e;
        }
    }

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

            DB::beginTransaction();

            try {
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
                Log::info('Created issue', ['issue_id' => $issue->id]);

                $user = auth()->user();
                $post = Post::create([
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

                Log::info('Created post', ['post_id' => $post->post_id, 'issue_id' => $post->issue_id]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Issue created successfully',
                    'issue' => $issue,
                    'post' => $post
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
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

    private function updateReactionCounts($issueId)
{
    $issueId = (int) $issueId; // Ensure integer type

    $reactionCounts = DB::table('issue_reactions')
        ->where('issue_id', $issueId)
        ->select('reaction_type', DB::raw('count(*) as count'))
        ->groupBy('reaction_type')
        ->get()
        ->keyBy('reaction_type');

    $reactionData = [
        'support_count' => $reactionCounts['support']->count ?? 0,
        'affected_count' => $reactionCounts['affected']->count ?? 0,
        'not_sure_count' => $reactionCounts['not_sure']->count ?? 0,
        'invalid_count' => $reactionCounts['invalid']->count ?? 0,
        'fixed_count' => $reactionCounts['fixed']->count ?? 0,
    ];

    $post = Post::where('issue_id', $issueId)->first();
    if ($post) {
        $post->update($reactionData);
        Log::info('Updated post reaction counts', ['post_id' => $post->post_id, 'reaction_counts' => $reactionData]);
    }

    $issue = Issue::find($issueId);
    if ($issue) {
        $issue->update($reactionData);
        Log::info('Updated issue reaction counts', ['issue_id' => $issueId, 'reaction_counts' => $reactionData]);
    }
}
    public function addReaction(Request $request, $id)
{
    try {
        Log::info('=== REACTION REQUEST START ===');
        Log::info('Incoming reaction request for issue: ' . $id);
        Log::info('Request data: ', $request->all());
        Log::info('User authenticated: ' . (auth()->check() ? 'Yes' : 'No'));

        if (auth()->check()) {
            Log::info('User ID: ' . auth()->id());
            Log::info('User name: ' . auth()->user()->username);
        }

        $validator = Validator::make($request->all(), [
            'reaction_type' => 'required|in:support,affected,not_sure,invalid,fixed'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed: ' . json_encode($validator->errors()));
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Convert issue ID to integer to match table structure
        $issueId = (int) $id;
        $issue = Issue::findOrFail($issueId);
        $userId = auth()->id();

        if (!$userId) {
            Log::error('Unauthenticated user attempting to react to issue ' . $issueId);
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $reactionType = $request->reaction_type;
        Log::info('Processing reaction type: ' . $reactionType . ' for issue: ' . $issueId . ' by user: ' . $userId);

        $existingReaction = DB::table('issue_reactions')
            ->where('issue_id', $issueId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->first();

        if ($existingReaction) {
            DB::table('issue_reactions')
                ->where('issue_id', $issueId)
                ->where('user_id', $userId)
                ->where('reaction_type', $reactionType)
                ->delete();

            $action = 'removed';
            Log::info('Reaction removed for user ' . $userId . ' on issue ' . $issueId . ' type: ' . $reactionType);
        } else {
            $userReactionCount = DB::table('issue_reactions')
                ->where('issue_id', $issueId)
                ->where('user_id', $userId)
                ->count();

            if ($userReactionCount >= 2) {
                Log::warning('User ' . $userId . ' reached reaction limit on issue ' . $issueId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only have 2 reactions per post'
                ], 400);
            }

            // DEBUG: Log before insertion
            Log::info('Attempting to insert reaction:', [
                'issue_id' => $issueId,
                'user_id' => $userId,
                'reaction_type' => $reactionType
            ]);

            try {
                $insertResult = DB::table('issue_reactions')->insert([
                    'issue_id' => $issueId,
                    'user_id' => $userId,
                    'reaction_type' => $reactionType,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info('Insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));

                if ($insertResult) {
                    // Create notification for the issue owner
                    if ($issue->user_id != $userId) {
                        $user = auth()->user();
                        $actionMessage = $reactionType === 'support' ? 'supported' : "supported $reactionType";

                        Notification::create([
                            'user_id' => $issue->user_id,
                            'author_id' => $userId,
                            'author_name' => $user->username,
                            'action' => $actionMessage,
                            'issue_id' => $issueId,
                            'issue_description' => $issue->report_type,
                            'created_at' => now(),
                        ]);
                        Log::info('Notification created for reaction', [
                            'issue_id' => $issueId,
                            'user_id' => $issue->user_id
                        ]);
                    }

                    $action = 'added';
                    Log::info('Reaction added successfully for user ' . $userId . ' on issue ' . $issueId . ' type: ' . $reactionType);
                } else {
                    Log::error('Failed to insert reaction into database');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to add reaction'
                    ], 500);
                }

            } catch (\Exception $e) {
                Log::error('Database insertion error: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ], 500);
            }
        }

        $this->updateReactionCounts($issueId);

        $reactionCounts = DB::table('issue_reactions')
            ->where('issue_id', $issueId)
            ->select('reaction_type', DB::raw('count(*) as count'))
            ->groupBy('reaction_type')
            ->get()
            ->keyBy('reaction_type')
            ->map(function ($item) {
                return ['count' => $item->count];
            })->toArray();

        $userReactions = DB::table('issue_reactions')
            ->where('issue_id', $issueId)
            ->where('user_id', $userId)
            ->pluck('reaction_type')
            ->toArray();

        Log::info('=== REACTION REQUEST END ===');

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction ' . $action . ' successfully',
            'reaction_counts' => $reactionCounts,
            'user_reactions' => $userReactions
        ]);

    } catch (\Exception $e) {
        Log::error('Reaction error for issue ' . $id . ': ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to process reaction',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function getUserReactions($id)
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'status' => 'success',
                    'user_reactions' => []
                ]);
            }

            $userReactions = DB::table('issue_reactions')
                ->where('issue_id', $id)
                ->where('user_id', $userId)
                ->pluck('reaction_type')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'user_reactions' => $userReactions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user reactions for issue ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get user reactions'
            ], 500);
        }
    }

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

            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $imagePath = null;
            if ($request->image) {
                $imagePath = $this->saveBase64Image($request->image, 'comments/');
            }

            $commentId = DB::table('issue_comments')->insertGetId([
                'issue_id' => $id,
                'user_id' => $userId,
                'comment' => $request->comment,
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $post = Post::where('issue_id', $id)->first();
            if ($post) {
                $post->increment('comment_count');
            }

            // Create notification for the issue owner
            if ($issue->user_id != $userId) { // Don't notify if user comments on their own issue
                $user = auth()->user();
                Notification::create([
                    'user_id' => $issue->user_id,
                    'author_id' => $userId,
                    'author_name' => $user->username,
                    'action' => 'commented',
                    'issue_id' => $id,
                    'issue_description' => $issue->report_type,
                    'created_at' => now(),
                ]);
                Log::info('Notification created for comment', ['issue_id' => $id, 'user_id' => $issue->user_id]);
            }

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
