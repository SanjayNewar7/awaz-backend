<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    // --------------------------------
    // District → Region → Ward dataset
    // --------------------------------
    private $locationsData = [
        "Kathmandu" => [
            "Kathmandu Metropolitan City" => 32,
            "Kirtipur Municipality" => 10,
            "Gokarneshwor Municipality" => 9,
            "Tokha Municipality" => 11,
            "Budhanilkantha Municipality" => 13,
            "Tarakeshwor Municipality" => 11,
            "Chandragiri Municipality" => 15,
            "Nagarjun Municipality" => 10,
            "Dakshinkali Municipality" => 9,
            "Shankharapur Municipality" => 9,
        ],
        "Lalitpur" => [
            "Lalitpur Metropolitan City" => 29,
            "Godawari Municipality" => 14,
            "Mahalaxmi Municipality" => 10,
            "Bagmati Rural Municipality" => 7,
            "Konjyosom Rural Municipality" => 5,
            "Mahankal Rural Municipality" => 6,
        ],
        "Bhaktapur" => [
            "Bhaktapur Municipality" => 10,
            "Changunarayan Municipality" => 9,
            "Madhyapur Thimi Municipality" => 9,
            "Suryabinayak Municipality" => 10,
        ],
        "Chitwan" => [
            "Bharatpur Metropolitan City" => 29,
            "Kalika Municipality" => 11,
            "Khairahani Municipality" => 13,
            "Madi Municipality" => 13,
            "Rapti Municipality" => 13,
            "Ratnanagar Municipality" => 16,
            "Ichchhakamana Rural Municipality" => 7,
        ],
        "Pokhara (Kaski)" => [
            "Pokhara Metropolitan City" => 33,
            "Annapurna Rural Municipality" => 11,
            "Machhapuchchhre Rural Municipality" => 9,
            "Madi Rural Municipality" => 9,
            "Rupa Rural Municipality" => 7,
        ],
        "Morang" => [
            "Biratnagar Metropolitan City" => 19,
            "Belbari Municipality" => 11,
            "Urlabari Municipality" => 9,
            "Letang Municipality" => 9,
            "Rangeli Municipality" => 9,
            "Sunwarshi Municipality" => 9,
            "Sundarharaicha Municipality" => 12,
        ],
        "Rupandehi" => [
            "Butwal Sub-Metropolitan City" => 19,
            "Siddharthanagar Municipality" => 13,
            "Devdaha Municipality" => 12,
            "Lumbini Sanskritik Municipality" => 13,
            "Sainamaina Municipality" => 12,
            "Tilottama Municipality" => 17,
        ],
        "Dang" => [
            "Ghorahi Sub-Metropolitan City" => 19,
            "Tulsipur Sub-Metropolitan City" => 19,
            "Lamahi Municipality" => 9,
            "Banglachuli Rural Municipality" => 9,
            "Rapti Rural Municipality" => 9,
        ],
        "Dharan (Sunsari)" => [
            "Dharan Sub-Metropolitan City" => 20,
            "Inaruwa Municipality" => 10,
            "Itahari Sub-Metropolitan City" => 20,
            "Barahachhetra Municipality" => 10,
            "Ramdhuni Municipality" => 9,
        ],
        "Birgunj (Parsa)" => [
            "Birgunj Metropolitan City" => 32,
            "Bahudarmai Municipality" => 9,
            "Parsagadhi Municipality" => 9,
            "Pokhariya Municipality" => 12,
            "Thori Rural Municipality" => 9,
        ],
    ];

    // -------------------------------
    // USER ANALYTICS
    // -------------------------------
    public function getUserAnalytics(Request $request)
    {
        try {
            $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $data   = [];
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = $date;
                $data[]   = 0;
            }
            foreach ($userGrowth as $growth) {
                $index = array_search($growth->date, $labels);
                if ($index !== false) {
                    $data[$index] = $growth->count;
                }
            }

            $verified   = User::where('is_verified', true)->count();
            $unverified = User::where('is_verified', false)->count();

            $recentUsers = User::latest()->take(5)->get([
                'user_id', 'username', 'created_at'
            ]);

            return response()->json([
                'status' => 'success',
                'user_growth' => ['labels' => $labels, 'data' => $data],
                'verification_stats' => [
                    'verified' => $verified,
                    'unverified' => $unverified
                ],
                'recent_users' => $recentUsers
            ]);
        } catch (\Exception $e) {
            Log::error('AnalyticsController@getUserAnalytics error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch analytics',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // -------------------------------
    // ISSUE ANALYTICS
    // -------------------------------

    public function index()
    {
        return view('analytics');
    }

    public function getAnalytics(Request $request)
    {
        try {
            $district   = $request->input('district');
            $regionType = $request->input('location');
            $ward       = $request->input('ward');

            // Base filtered query for charts (Report Type / Status / Summary)
            $filteredQuery = Issue::query();
            if ($district)   $filteredQuery->where('district', $district);
            if ($regionType) $filteredQuery->where('location', $regionType);
            if ($ward)       $filteredQuery->where('ward', $ward);

            $issues = $filteredQuery->get();

            // Top 3 districts by issue count (overall, not filter-bound)
            $topDistricts = Issue::select('district', DB::raw('COUNT(*) as issue_count'))
                ->groupBy('district')
                ->orderByDesc('issue_count')
                ->take(3)
                ->get();

            // Report types count (RESPECT filters)
            $reportTypes = (clone $filteredQuery)
                ->select('report_type', DB::raw('COUNT(*) as count'))
                ->groupBy('report_type')
                ->get();

            // Status counts (RESPECT filters)
            $statusCounts = [
                'pending' => (clone $filteredQuery)->where('status', 'Pending')->count(),
                'fixed'   => (clone $filteredQuery)->where('status', 'Fixed')->count(),
            ];

            // Totals (RESPECT filters)
            $supportCount  = $issues->sum('support_count');
            $affectedCount = $issues->sum('affected_count');

            // -------------------------------
            // Issues by Region (using "location")
            // - On initial load (no district): overall grouped by location
            // - After selecting a district: grouped by location restricted to that district
            // (ignores region/ward for this section as requested)
            // -------------------------------
            $issuesByRegionQuery = Issue::query()->whereNotNull('location')->where('location', '<>', '');
            if ($district) {
                $issuesByRegionQuery->where('district', $district);
            }

            $locations = $issuesByRegionQuery
                ->select('location')
                ->groupBy('location')
                ->get();

            $issuesByRegion = $locations->map(function ($row) use ($district) {
                $q = Issue::where('location', $row->location);
                if ($district) $q->where('district', $district);

                $issuesInLocation = $q->get([
                    'heading', 'description', 'report_type', 'district', 'ward', 'photo1'
                ]);

                return [
                    'region'      => $row->location,  // shown as card title
                    'issue_count' => $issuesInLocation->count(),
                    'issues'      => $issuesInLocation,
                ];
            })->values();

            return response()->json([
                'status'           => 'success',
                'issues'           => $issues,               // filtered issues
                'top_districts'    => $topDistricts,         // overall
                'report_types'     => $reportTypes,          // filtered
                'status_counts'    => $statusCounts,         // filtered
                'total_issues'     => $issues->count(),      // filtered
                'support_count'    => $supportCount,         // filtered
                'affected_count'   => $affectedCount,        // filtered
                'issues_by_region' => $issuesByRegion,       // overall or district-restricted per requirement
            ]);

        } catch (\Exception $e) {
            Log::error('AnalyticsController@getAnalytics error: '.$e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch analytics',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // -------------------------------
    // GET REGIONS (Municipalities)
    // -------------------------------
    public function getRegions($district)
    {
        if (isset($this->locationsData[$district])) {
            return response()->json([
                'status'  => 'success',
                'regions' => $this->locationsData[$district]
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'District not found'
        ], 404);
    }

    // -------------------------------
    // GET WARDS
    // -------------------------------
    public function getWards($district, $region)
    {
        if (isset($this->locationsData[$district][$region])) {
            $numWards = $this->locationsData[$district][$region];
            $wards = range(1, $numWards);

            return response()->json([
                'status' => 'success',
                'wards'  => $wards
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Region not found'
        ], 404);
    }

    // -------------------------------
    // COMMUNITY ENGAGEMENT ANALYTICS
    // -------------------------------
    public function getCommunityEngagement(Request $request)
    {
        try {
            // Calculate total engagements (e.g., sum of support_count from issues)
            $totalEngagements = Issue::sum('support_count');

            // Calculate active participants (e.g., count of unique users who reported issues)
            $activeParticipants = Issue::select('user_id')
                ->distinct()
                ->whereNotNull('user_id')
                ->count();

            return response()->json([
                'status' => 'success',
                'community_engagement' => [
                    'total' => $totalEngagements,
                    'participants' => $activeParticipants
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AnalyticsController@getCommunityEngagement error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch community engagement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // -------------------------------
    // TOP REPORTERS ANALYTICS
    // -------------------------------
    public function getTopReporters(Request $request)
    {
        try {
            // Fetch top 5 reporters based on the number of issues reported
            $topReporters = Issue::select('user_id', DB::raw('COUNT(*) as report_count'))
                ->groupBy('user_id')
                ->orderByDesc('report_count')
                ->take(5)
                ->get();

            // Map user IDs to usernames (assuming a User model relationship or join)
            $topReporters = $topReporters->map(function ($reporter) {
                $user = User::find($reporter->user_id);
                return [
                    'username' => $user ? $user->username : 'Unknown',
                    'report_count' => $reporter->report_count
                ];
            });

            return response()->json([
                'status' => 'success',
                'top_reporters' => $topReporters
            ]);
        } catch (\Exception $e) {
            Log::error('AnalyticsController@getTopReporters error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch top reporters',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
