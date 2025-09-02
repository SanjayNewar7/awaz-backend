<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'admin' && $password === 'admin') {
            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors(['credentials' => 'Invalid username or password'])->withInput();
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function users()
    {
        return view('users');
    }

    public function analytics()
    {
        return view('analytics');
    }

    public function issues()
    {
        return view('issue');
    }
    public function verification()
    {
        return view('verification');
    }

    public function logout(Request $request)
    {
        return redirect()->route('superadmin.login');
    }

    public function store(Request $request)
    {
        try {
            Log::info('Incoming registration request data: ', $request->all());
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:users',
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:100|unique:users',
                'phone_number' => 'required|string|size:10',
                'password' => 'required|string|min:8|confirmed',
                'district' => 'required|string|max:50',
                'city' => 'required|string|max:50',
                'ward' => 'required|integer',
                'area_name' => 'required|string|max:100',
                'citizenship_id_number' => 'required|string|max:50|unique:users',
                'gender' => 'required|in:Male,Female,Other',
                'is_verified' => 'required|boolean',
                'agreed_to_terms' => 'required|boolean',
                'citizenship_front_image' => 'required|string',
                'citizenship_back_image' => 'required|string',
            ]);

            // Set default profile image based on gender
            $profileImagePath = $validated['gender'] === 'Male' ? 'images/users/male_avatar.png' : 'images/users/female_avatar.png';

            // Process citizenship images
            $citizenshipFrontImagePath = $this->saveBase64Image($validated['citizenship_front_image'], 'users/citizenship_front_');
            $citizenshipBackImagePath = $this->saveBase64Image($validated['citizenship_back_image'], 'users/citizenship_back_');

            $user = User::create([
                'username' => $validated['username'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'password_hash' => bcrypt($validated['password']),
                'district' => $validated['district'],
                'city' => $validated['city'],
                'ward' => $validated['ward'],
                'area_name' => $validated['area_name'],
                'citizenship_id_number' => $validated['citizenship_id_number'],
                'gender' => $validated['gender'],
                'is_verified' => $validated['is_verified'],
                'agreed_to_terms' => $validated['agreed_to_terms'],
                'citizenship_front_image' => $citizenshipFrontImagePath,
                'citizenship_back_image' => $citizenshipBackImagePath,
                'profile_image' => $profileImagePath,
            ]);

            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

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

    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validated = $request->validate([
                'username' => 'sometimes|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
                'first_name' => 'sometimes|string|max:50',
                'last_name' => 'sometimes|string|max:50',
                'email' => 'sometimes|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
                'phone_number' => 'sometimes|string|size:10',
                'district' => 'sometimes|string|max:50',
                'city' => 'sometimes|string|max:50',
                'ward' => 'sometimes|integer',
                'area_name' => 'sometimes|string|max:100',
                'citizenship_id_number' => 'sometimes|string|max:50|unique:users,citizenship_id_number,' . $user->user_id . ',user_id',
                'gender' => 'sometimes|in:Male,Female,Other',
                'is_verified' => 'sometimes|boolean',
                'agreed_to_terms' => 'sometimes|boolean',
                'citizenship_front_image' => 'sometimes|string',
                'citizenship_back_image' => 'sometimes|string',
                'profile_image' => 'sometimes|string',
            ]);

            $profileImagePath = $user->profile_image;
            if (isset($validated['profile_image'])) {
                if ($user->profile_image && !in_array($user->profile_image, ['images/users/male_avatar.png', 'images/users/female_avatar.png'])) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $profileImagePath = $this->saveBase64Image($validated['profile_image'], 'users/profile_');
            } elseif (isset($validated['gender']) && $validated['gender'] !== $user->gender) {
                $profileImagePath = $validated['gender'] === 'Male' ? 'images/users/male_avatar.png' : 'images/users/female_avatar.png';
            }

            $citizenshipFrontImagePath = $user->citizenship_front_image;
            if (isset($validated['citizenship_front_image'])) {
                if ($user->citizenship_front_image) {
                    Storage::disk('public')->delete($user->citizenship_front_image);
                }
                $citizenshipFrontImagePath = $this->saveBase64Image($validated['citizenship_front_image'], 'users/citizenship_front_');
            }

            $citizenshipBackImagePath = $user->citizenship_back_image;
            if (isset($validated['citizenship_back_image'])) {
                if ($user->citizenship_back_image) {
                    Storage::disk('public')->delete($user->citizenship_back_image);
                }
                $citizenshipBackImagePath = $this->saveBase64Image($validated['citizenship_back_image'], 'users/citizenship_back_');
            }

            $user->update([
                'username' => $validated['username'] ?? $user->username,
                'first_name' => $validated['first_name'] ?? $user->first_name,
                'last_name' => $validated['last_name'] ?? $user->last_name,
                'email' => $validated['email'] ?? $user->email,
                'phone_number' => $validated['phone_number'] ?? $user->phone_number,
                'district' => $validated['district'] ?? $user->district,
                'city' => $validated['city'] ?? $user->city,
                'ward' => $validated['ward'] ?? $user->ward,
                'area_name' => $validated['area_name'] ?? $user->area_name,
                'citizenship_id_number' => $validated['citizenship_id_number'] ?? $user->citizenship_id_number,
                'gender' => $validated['gender'] ?? $user->gender,
                'is_verified' => $validated['is_verified'] ?? $user->is_verified,
                'agreed_to_terms' => $validated['agreed_to_terms'] ?? $user->agreed_to_terms,
                'citizenship_front_image' => $citizenshipFrontImagePath,
                'citizenship_back_image' => $citizenshipBackImagePath,
                'profile_image' => $profileImagePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($validated['current_password'], $user->password_hash)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            $user->update([
                'password_hash' => bcrypt($validated['new_password'])
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Password change error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to change password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $users = User::paginate($perPage);

            return response()->json([
                'status' => 'success',
                'users' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
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
                    'bio' => $user->bio ?? 'Hello, Namaste everyone',
                    'profile_image' => $user->profile_image,
                    'posts_count' => $user->posts()->count(),
                    'likes_count' => 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }


    public function getUserdetail($userId)
{
    try {
        $user = User::findOrFail($userId);

        // Helper to format image URLs
        $formatImage = function ($path) {
            if (!$path) {
                return null;
            }

            // If it's already a full URL, just return it
            if (preg_match('/^http(s)?:\/\//', $path)) {
                return $path;
            }

            // Otherwise, make it a proper asset URL
            return asset('storage/' . ltrim(str_replace('storage/', '', $path), '/'));
        };

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
                'profile_image' => $formatImage($user->profile_image),
                'posts_count' => $user->posts()->count(),
                'citizenship_id_number' => $user->citizenship_id_number,
                'gender' => (string) $user->gender,
                'likes_count' => 0,
                'is_verified' => (bool) $user->is_verified,
                'agreed_to_terms' => (bool) $user->agreed_to_terms,
                'citizenship_front_image' => $formatImage($user->citizenship_front_image),
                'citizenship_back_image' => $formatImage($user->citizenship_back_image),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 404);
    }
}


    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('query');

            $users = User::where('username', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->orWhere('phone_number', 'like', "%$query%")
                ->orWhere('citizenship_id_number', 'like', "%$query%")
                ->limit(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Search failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed'
            ], 500);
        }
    }

    public function updateUserweb(Request $request, $userId)
    {
        try {
            $user = User::where('user_id', $userId)->firstOrFail();

            $validated = $request->validate([
                'username' => 'sometimes|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
                'first_name' => 'sometimes|string|max:50',
                'last_name' => 'sometimes|string|max:50',
                'email' => 'sometimes|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
                'phone_number' => 'sometimes|string|size:10',
                'district' => 'sometimes|string|max:50',
                'city' => 'sometimes|string|max:50',
                'ward' => 'sometimes|integer',
                'area_name' => 'sometimes|string|max:100',
                'citizenship_id_number' => 'sometimes|string|max:50|unique:users,citizenship_id_number,' . $user->user_id . ',user_id',
                'gender' => 'sometimes|in:Male,Female,Other',
                'is_verified' => 'sometimes|boolean',
                'agreed_to_terms' => 'sometimes|boolean',
                'citizenship_front_image' => 'sometimes|string',
                'citizenship_back_image' => 'sometimes|string',
                'profile_image' => 'sometimes|string',
            ]);

            // Handle image uploads
            $profileImagePath = $user->profile_image;
            if (isset($validated['profile_image'])) {
                if ($user->profile_image && !in_array($user->profile_image, ['images/users/male_avatar.png', 'images/users/female_avatar.png'])) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $profileImagePath = $this->saveBase64Image($validated['profile_image'], 'users/profile_');
            } elseif (isset($validated['gender']) && $validated['gender'] !== $user->gender) {
                $profileImagePath = $validated['gender'] === 'Male' ? 'images/users/male_avatar.png' : 'images/users/female_avatar.png';
            }

            $citizenshipFrontImagePath = $user->citizenship_front_image;
            if (isset($validated['citizenship_front_image'])) {
                if ($user->citizenship_front_image) {
                    Storage::disk('public')->delete($user->citizenship_front_image);
                }
                $citizenshipFrontImagePath = $this->saveBase64Image($validated['citizenship_front_image'], 'users/citizenship_front_');
            }

            $citizenshipBackImagePath = $user->citizenship_back_image;
            if (isset($validated['citizenship_back_image'])) {
                if ($user->citizenship_back_image) {
                    Storage::disk('public')->delete($user->citizenship_back_image);
                }
                $citizenshipBackImagePath = $this->saveBase64Image($validated['citizenship_back_image'], 'users/citizenship_back_');
            }

            // Update user
            $user->update([
                'username' => $validated['username'] ?? $user->username,
                'first_name' => $validated['first_name'] ?? $user->first_name,
                'last_name' => $validated['last_name'] ?? $user->last_name,
                'email' => $validated['email'] ?? $user->email,
                'phone_number' => $validated['phone_number'] ?? $user->phone_number,
                'district' => $validated['district'] ?? $user->district,
                'city' => $validated['city'] ?? $user->city,
                'ward' => $validated['ward'] ?? $user->ward,
                'area_name' => $validated['area_name'] ?? $user->area_name,
                'citizenship_id_number' => $validated['citizenship_id_number'] ?? $user->citizenship_id_number,
                'gender' => $validated['gender'] ?? $user->gender,
                'is_verified' => $validated['is_verified'] ?? $user->is_verified,
                'agreed_to_terms' => $validated['agreed_to_terms'] ?? $user->agreed_to_terms,
                'citizenship_front_image' => $citizenshipFrontImagePath,
                'citizenship_back_image' => $citizenshipBackImagePath,
                'profile_image' => $profileImagePath,
            ]);

            // Format image URLs for response
            $formatImage = function ($path) {
                if (!$path) return null;
                return preg_match('/^http(s)?:\/\//', $path) ? $path : asset('storage/' . ltrim(str_replace('storage/', '', $path), '/'));
            };

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
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
                    'profile_image' => $formatImage($user->profile_image),
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'gender' => (string) $user->gender,
                    'is_verified' => (bool) $user->is_verified,
                    'agreed_to_terms' => (bool) $user->agreed_to_terms,
                    'citizenship_front_image' => $formatImage($user->citizenship_front_image),
                    'citizenship_back_image' => $formatImage($user->citizenship_back_image),
                    'posts_count' => $user->posts()->count(),
                    'likes_count' => $user->likes_count ?? 0,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('SuperAdmin updateUser error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function userLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required_without:email|string|max:50',
                'email' => 'required_without:username|email|max:100',
                'password' => 'required|string|min:8',
            ]);

            $field = filter_var($credentials['username'] ?? $credentials['email'], FILTER_VALIDATE_EMAIL)
                ? 'email'
                : 'username';

            $user = User::where($field, $credentials[$field])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('User login error: ' . $e->getMessage());
            return response()->json(['message' => 'Login failed'], 500);
        }
    }

    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();

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
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'profile_image' => $user->profile_image,
                    'citizenship_front_image' => $user->citizenship_front_image,
                    'citizenship_back_image' => $user->citizenship_back_image,
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'is_verified' => (bool)$user->is_verified,
                    'likes_count' => $user->likes_count ?? 0,
                    'posts_count' => $user->posts_count ?? 0,
                    'verification_status' => $user->verification_status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user data'
            ], 500);
        }
    }
}
