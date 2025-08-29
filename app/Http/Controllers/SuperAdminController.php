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
}
