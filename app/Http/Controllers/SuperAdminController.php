<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        // Apply Sanctum authentication middleware for superadmin guard
        $this->middleware('auth:sanctum');
    }

    public function getUsers(Request $request)
    {
        try {
            $perPage = $request->input('limit', 10);
            $page = $request->input('page', 1);

            $users = User::paginate($perPage, ['*'], 'page', $page);

            $usersData = $users->map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'district' => $user->district,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'phone_number' => $user->phone_number,
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'citizenship_front_image' => $user->citizenship_front_image,
                    'citizenship_back_image' => $user->citizenship_back_image,
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'is_verified' => (bool)$user->is_verified,
                    'agreed_to_terms' => (bool)$user->agreed_to_terms,
                    'city' => $user->city,
                    'likes_count' => $user->likes_count ?? 0,
                    'posts_count' => $user->posts()->count(),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'users' => $usersData,
                'pages' => $users->lastPage(),
                'page' => $users->currentPage(),
                'total' => $users->total(),
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin getUsers error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser($userId)
    {
        try {
            $user = User::where('user_id', $userId)->firstOrFail();

            return response()->json([
                'status' => 'success',
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'district' => $user->district,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'phone_number' => $user->phone_number,
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'citizenship_front_image' => $user->citizenship_front_image,
                    'citizenship_back_image' => $user->citizenship_back_image,
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'is_verified' => (bool)$user->is_verified,
                    'agreed_to_terms' => (bool)$user->agreed_to_terms,
                    'city' => $user->city,
                    'likes_count' => $user->likes_count ?? 0,
                    'posts_count' => $user->posts()->count(),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin getUser error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('q');

            if (!$query) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Search query is required'
                ], 400);
            }

            $users = User::where('phone_number', 'like', "%$query%")
                ->orWhere('username', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->orWhere('citizenship_id_number', 'like', "%$query%")
                ->limit(10)
                ->get();

            $usersData = $users->map(function ($user) {
                return [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'district' => $user->district,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'phone_number' => $user->phone_number,
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'citizenship_front_image' => $user->citizenship_front_image,
                    'citizenship_back_image' => $user->citizenship_back_image,
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'is_verified' => (bool)$user->is_verified,
                    'agreed_to_terms' => (bool)$user->agreed_to_terms,
                    'city' => $user->city,
                    'likes_count' => $user->likes_count ?? 0,
                    'posts_count' => $user->posts()->count(),
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'users' => $usersData
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin searchUsers error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleVerification(Request $request, $userId)
    {
        try {
            $user = User::where('user_id', $userId)->firstOrFail();
            $user->is_verified = !$user->is_verified;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User verification status updated',
                'is_verified' => (bool)$user->is_verified
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin toggleVerification error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update verification status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::where('user_id', $userId)->firstOrFail();

            // Delete associated images
            if ($user->citizenship_front_image) {
                Storage::disk('public')->delete(str_replace(Storage::url(''), '', $user->citizenship_front_image));
            }
            if ($user->citizenship_back_image) {
                Storage::disk('public')->delete(str_replace(Storage::url(''), '', $user->citizenship_back_image));
            }
            if ($user->profile_image && !in_array($user->profile_image, ['images/users/male_avatar.png', 'images/users/female_avatar.png'])) {
                Storage::disk('public')->delete(str_replace(Storage::url(''), '', $user->profile_image));
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin deleteUser error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    public function sendWarning(Request $request, $userId)
    {
        try {
            $user = User::where('user_id', $userId)->firstOrFail();
            $validated = $request->validate([
                'message' => 'required|string|max:255',
            ]);

            Notification::create([
                'user_id' => $user->user_id,
                'author_id' => Auth::guard('sanctum')->id(),
                'author_name' => Auth::guard('sanctum')->user()->username,
                'action' => 'warning',
                'issue_description' => $validated['message'],
                'is_read' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Warning sent to user'
            ]);
        } catch (\Exception $e) {
            Log::error('SuperAdmin sendWarning error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send warning',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkAuth()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated as superadmin'
        ]);
    }

    // Add these methods to your SuperAdminController

public function usersIndex()
{
    return view('superadmin.users');
}

public function issuesIndex()
{
    return view('issues');
}

public function verificationIndex()
{
    return view('verification');
}

public function notificationsIndex()
{
    return view('notifications');
}

public function settingsIndex()
{
    return view('settings');
}

public function getUserPosts($userId)
{
    try {
        $user = User::where('user_id', $userId)->firstOrFail();
        $posts = $user->posts()->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'created_at' => $post->created_at,
            ];
        });
        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ]);
    } catch (\Exception $e) {
        Log::error('SuperAdmin getUserPosts error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch user posts',
            'error' => $e->getMessage()
        ], 500);
    }
}

// Add these methods to your SuperAdminController

public function getUserAnalytics(Request $request)
{
    try {
        // Get user growth for last 14 days
        $userGrowth = User::select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(14))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Fill in missing dates with 0 counts
        $userGrowthData = [];
        $dates = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $userGrowthData[$date] = 0;
        }

        foreach ($userGrowth as $growth) {
            $formattedDate = \Carbon\Carbon::parse($growth->date)->format('Y-m-d');
            $userGrowthData[$formattedDate] = $growth->count;
        }

        // Get verification stats
        $verified = User::where('is_verified', true)->count();
        $unverified = User::where('is_verified', false)->count();

        // Get recent users (last 5)
        $recentUsers = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'user_growth' => [
                'labels' => $dates,
                'data' => array_values($userGrowthData)
            ],
            'verification_stats' => [
                'verified' => $verified,
                'unverified' => $unverified
            ],
            'recent_users' => $recentUsers
        ]);
    } catch (\Exception $e) {
        Log::error('SuperAdmin getUserAnalytics error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch analytics',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getUserGrowthChart(Request $request)
{
    try {
        $days = $request->input('days', 14); // Default to 14 days

        $userGrowth = User::select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays($days))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Fill in missing dates
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $data[] = 0;
        }

        foreach ($userGrowth as $growth) {
            $formattedDate = \Carbon\Carbon::parse($growth->date)->format('Y-m-d');
            $index = array_search($formattedDate, $labels);
            if ($index !== false) {
                $data[$index] = $growth->count;
            }
        }

        return response()->json([
            'status' => 'success',
            'labels' => $labels,
            'data' => $data
        ]);
    } catch (\Exception $e) {
        Log::error('SuperAdmin getUserGrowthChart error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch user growth data'
        ], 500);
    }
}

public function getVerificationStats()
{
    try {
        $verified = User::where('is_verified', true)->count();
        $unverified = User::where('is_verified', false)->count();

        return response()->json([
            'status' => 'success',
            'verified' => $verified,
            'unverified' => $unverified
        ]);
    } catch (\Exception $e) {
        Log::error('SuperAdmin getVerificationStats error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch verification stats'
        ], 500);
    }
}
}
