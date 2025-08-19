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

    public function logout(Request $request)
    {
        return redirect()->route('superadmin.login');
    }

    /**
     * Register a new user
     */
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
                'citizenship_front_image' => 'required|string', // Base64
                'citizenship_back_image' => 'required|string' // Base64
            ]);

            // Set default profile image based on gender
            $profileImagePath = $validated['gender'] === 'Male' ? 'images/male_avatar.jpeg' : 'images/female_avatar.jpeg';

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
                'profile_image' => $profileImagePath, // Add default profile image
            ]);

            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Save base64 image to storage
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

    /**
     * Update user profile
     */
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
                'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
                'first_name' => 'sometimes|string|max:50',
                'last_name' => 'sometimes|string|max:50',
                'email' => 'sometimes|email|max:100|unique:users,email,' . $user->id,
                'phone_number' => 'sometimes|string|size:10',
                'district' => 'sometimes|string|max:50',
                'city' => 'sometimes|string|max:50',
                'ward' => 'sometimes|integer',
                'area_name' => 'sometimes|string|max:100',
                'citizenship_id_number' => 'sometimes|string|max:50|unique:users,citizenship_id_number,' . $user->id,
                'gender' => 'sometimes|in:Male,Female,Other',
                'is_verified' => 'sometimes|boolean',
                'agreed_to_terms' => 'sometimes|boolean',
                'citizenship_front_image' => 'sometimes|string', // Base64
                'citizenship_back_image' => 'sometimes|string', // Base64
                'profile_image' => 'sometimes|string', // Base64
            ]);

            // Handle profile image
            $profileImagePath = $user->profile_image;
            if (isset($validated['profile_image'])) {
                // Delete old profile image if it's not a default avatar
                if ($user->profile_image && !in_array($user->profile_image, ['images/male_avatar.jpeg', 'images/female_avatar.jpeg'])) {
                    Storage::delete('public/' . $user->profile_image);
                }
                $profileImagePath = $this->saveBase64Image($validated['profile_image'], 'users/profile_');
            } elseif (isset($validated['gender']) && $validated['gender'] !== $user->gender) {
                // Update default avatar if gender changes
                $profileImagePath = $validated['gender'] === 'Male' ? 'images/male_avatar.jpeg' : 'images/female_avatar.jpeg';
            }

            // Handle citizenship images
            $citizenshipFrontImagePath = $user->citizenship_front_image;
            if (isset($validated['citizenship_front_image'])) {
                if ($user->citizenship_front_image) {
                    Storage::delete('public/' . $user->citizenship_front_image);
                }
                $citizenshipFrontImagePath = $this->saveBase64Image($validated['citizenship_front_image'], 'users/citizenship_front_');
            }

            $citizenshipBackImagePath = $user->citizenship_back_image;
            if (isset($validated['citizenship_back_image'])) {
                if ($user->citizenship_back_image) {
                    Storage::delete('public/' . $user->citizenship_back_image);
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

    /**
     * Get all users (paginated)
     */
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

    /**
     * Get single user by ID
     */
    public function getUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('User not found: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Search users
     */
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

    /**
     * User login with Sanctum token
     */
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

            // Generate proper Sanctum token
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
                'citizenship_front_image' => $user->citizenship_front_image,
                'citizenship_back_image' => $user->citizenship_back_image,
                'is_verified' => (bool)$user->is_verified,
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

