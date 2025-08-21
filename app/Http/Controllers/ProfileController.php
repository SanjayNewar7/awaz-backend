<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getCurrentUser()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'district' => $user->district,
                    'city' => $user->city,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'phone_number' => $user->phone_number,
                    'email' => $user->email,
                    'bio' => $user->bio ?? 'Hello, Namaste everyone',
                    'profile_image' => $user->profile_image ? str_replace('public/', '', $user->profile_image) : null,
                    'posts_count' => $user->posts()->count(), // Assuming a posts relationship
                    'likes_count' => 0 // Placeholder; implement logic if needed
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user data'
            ], 500);
        }
    }

    public function getUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            return response()->json([
                'status' => 'success',
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'district' => $user->district,
                    'city' => $user->city,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'phone_number' => $user->phone_number,
                    'email' => $user->email,
                    'bio' => $user->bio ?? 'No bio available',
                    'profile_image' => $user->profile_image ? str_replace('public/', '', $user->profile_image) : null,
                    'posts_count' => $user->posts()->count(), // Assuming a posts relationship
                    'likes_count' => 0 // Placeholder; implement logic if needed
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validated = $request->validate([
                'district' => 'sometimes|string|max:50',
                'city' => 'sometimes|string|max:50',
                'ward' => 'sometimes|integer',
                'area_name' => 'sometimes|string|max:100',
                'phone_number' => 'sometimes|string|size:10',
                'email' => 'sometimes|email|max:100|unique:users,email,'.$user->user_id.',user_id',
                'bio' => 'sometimes|string|max:120'
            ]);

            $user->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }
}
