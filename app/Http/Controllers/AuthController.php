<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
        } else {
            return back()->withErrors(['credentials' => 'Invalid username or password'])->withInput();
        }
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        return redirect()->route('superadmin.login');
    }

    public function getUsers(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        $users = User::skip($offset)->take($limit)->get();
        $total = User::count();
        $pages = ceil($total / $limit);

        return response()->json([
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    public function searchUsers(Request $request)
    {
        $searchTerm = $request->input('q');

        if (!$searchTerm) {
            return response()->json(['users' => [], 'message' => 'No search term provided'], 400);
        }

        Log::info('Search term received: ' . $searchTerm);

        try {
            $users = User::where(function ($query) use ($searchTerm) {
                $query->where('user_id', '=', $searchTerm) // Exact match for user_id
                      ->orWhere('username', 'like', '%' . strtolower($searchTerm) . '%') // Case-insensitive username
                      ->orWhere('phone_number', 'like', '%' . $searchTerm . '%')
                      ->orWhere('citizenship_id_number', 'like', '%' . $searchTerm . '%');
            })->get();

            Log::info('Search query executed, found ' . $users->count() . ' users');

            if ($users->isEmpty()) {
                Log::warning('No users found for search term: ' . $searchTerm);
                return response()->json(['users' => [], 'message' => 'No user found with that identifier']);
            }

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json(['users' => [], 'message' => 'Error searching users: ' . $e->getMessage()], 500);
        }
    }

    public function getUser($userId)
    {
        try {
            Log::info('Fetching user with ID: ' . $userId);
            $user = User::find($userId);

            if ($user) {
                Log::info('User found: ' . $user->username);
                return response()->json($user);
            } else {
                Log::warning('User not found for ID: ' . $userId);
                return response()->json(['message' => 'User not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching user: ' . $e->getMessage()], 500);
        }
    }
}
