<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class VerificationController extends Controller
{


    public function index()
{
    return view('verification');
}
    /**
     * Fetch users for verification with pagination, filters, and stats.
     */
    public function getUsers(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 8);
            $status = $request->input('status');
            $search = $request->input('search');

            // Base query for users who have submitted verification (citizenship_id_number not null)
            $query = User::whereNotNull('citizenship_id_number');

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('citizenship_id_number', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($status === 'verified') {
                $query->where('is_verified', 1);
            } elseif ($status === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('verification_status')
                      ->orWhere('verification_status', 'pending');
                })->where('is_verified', 0);
            } elseif ($status === 'rejected') {
                $query->where('verification_status', 'rejected')
                      ->where('is_verified', 0);
            }

            // Paginate results
            $users = $query->paginate($limit, ['*'], 'page', $page);

            // Calculate stats (overall, not filtered)
            $statsQuery = User::whereNotNull('citizenship_id_number');
            $stats = [
                'total' => $statsQuery->count(),
                'verified' => $statsQuery->clone()->where('is_verified', 1)->count(),
                'pending' => $statsQuery->clone()->where(function ($q) {
                    $q->whereNull('verification_status')
                      ->orWhere('verification_status', 'pending');
                })->where('is_verified', 0)->count(),
                'rejected' => $statsQuery->clone()->where('verification_status', 'rejected')->where('is_verified', 0)->count(),
            ];

            return response()->json([
                'status' => 'success',
                'users' => $users,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('VerificationController@getUsers error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users',
            ], 500);
        }
    }

    /**
     * Fetch details for a specific user.
     */
    public function getUser($id)
    {
        try {
            $user = User::withCount('posts')->findOrFail($id);

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
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'gender' => (string)$user->gender,
                    'district' => $user->district,
                    'city' => $user->city,
                    'ward' => $user->ward,
                    'area_name' => $user->area_name,
                    'citizenship_id_number' => $user->citizenship_id_number,
                    'is_verified' => (bool)$user->is_verified,
                    'verification_status' => $user->verification_status,
                    'posts_count' => $user->posts_count,
                    'profile_image' => $formatImage($user->profile_image),
                    'citizenship_front_image' => $formatImage($user->citizenship_front_image),
                    'citizenship_back_image' => $formatImage($user->citizenship_back_image),
                    'created_at' => $user->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('VerificationController@getUser error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
    }

    /**
     * Update user verification status.
     */
    public function verifyUser(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,verified,rejected',
                'reason' => 'nullable|string|max:255',
            ]);

            $user = User::findOrFail($id);
            $status = $request->status;

            $user->verification_status = $status;
            $user->is_verified = ($status === 'verified');
            if ($status === 'rejected' && $request->reason) {
                $user->verification_note = $request->reason; // Assuming a verification_note column for reasons
            }
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Verification updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('VerificationController@verifyUser error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update verification',
            ], 500);
        }
    }

    /**
     * Export users to CSV.
     */
    public function export()
    {
        try {
            $users = User::whereNotNull('citizenship_id_number')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="user_verifications.csv"',
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Username', 'Full Name', 'Email', 'Phone', 'District', 'City', 'Ward', 'Citizenship ID', 'Status', 'Joined']);

                foreach ($users as $user) {
                    $status = $user->is_verified ? 'Approved' : ($user->verification_status ?? 'Pending');
                    fputcsv($file, [
                        $user->user_id,
                        $user->username,
                        $user->first_name . ' ' . $user->last_name,
                        $user->email,
                        $user->phone_number,
                        $user->district,
                        $user->city,
                        $user->ward,
                        $user->citizenship_id_number,
                        $status,
                        $user->created_at->format('Y-m-d'),
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('VerificationController@export error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export users',
            ], 500);
        }
    }
}
