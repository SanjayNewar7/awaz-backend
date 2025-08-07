<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $users = User::paginate($limit, ['*'], 'page', $page);

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
                'is_verified' => $user->is_verified,
                'agreed_to_terms' => $user->agreed_to_terms,
                'city' => $user->city,
                'likes_count' => $user->likes_count ?? 0,
                'posts_count' => $user->posts_count ?? 0,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json([
            'users' => $usersData,
            'total' => $users->total(),
            'pages' => $users->lastPage(),
            'page' => $users->currentPage(),
        ]);
    }

    public function show($id)
    {
        $user = User::where('user_id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userData = [
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
            'is_verified' => $user->is_verified,
            'agreed_to_terms' => $user->agreed_to_terms,
            'city' => $user->city,
            'likes_count' => $user->likes_count ?? 0,
            'posts_count' => $user->posts_count ?? 0,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return response()->json($userData);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('user_id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|unique:users,username,' . $user->user_id . ',user_id',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|integer',
            'area_name' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^\d{10}$/',
            'gender' => 'nullable|in:Male,Female',
            'email' => 'sometimes|string|email|unique:users,email,' . $user->user_id . ',user_id',
            'password_hash' => 'sometimes|string|min:8',
            'citizenship_front_image' => 'sometimes|string',
            'citizenship_back_image' => 'sometimes|string',
            'citizenship_id_number' => 'sometimes|string|unique:users,citizenship_id_number,' . $user->user_id . ',user_id',
            'agreed_to_terms' => 'sometimes|boolean',
            'city' => 'sometimes|string',
            'likes_count' => 'sometimes|integer',
            'posts_count' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->only([
            'username', 'first_name', 'last_name', 'district', 'ward', 'area_name',
            'phone_number', 'gender', 'email', 'citizenship_front_image',
            'citizenship_back_image', 'citizenship_id_number', 'agreed_to_terms', 'city',
            'likes_count', 'posts_count',
        ]);

        if ($request->has('password_hash')) {
            $updateData['password_hash'] = Hash::make($request->password_hash);
        }

        $user->update($updateData);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::where('user_id', $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $users = User::where('user_id', $query)
            ->orWhere('username', 'like', "%$query%")
            ->orWhere('phone_number', 'like', "%$query%")
            ->orWhere('citizenship_id_number', 'like', "%$query%")
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
                'is_verified' => $user->is_verified,
                'agreed_to_terms' => $user->agreed_to_terms,
                'city' => $user->city,
                'likes_count' => $user->likes_count ?? 0,
                'posts_count' => $user->posts_count ?? 0,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json(['users' => $usersData]);
    }
}
