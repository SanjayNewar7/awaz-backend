<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use Illuminate\Support\Facades\DB;

class IssueWebController extends Controller
{
    // Issues grouped by report_type
    public function getIssuesByType()
    {
        $data = Issue::select('report_type', DB::raw('COUNT(*) as count'))
            ->groupBy('report_type')
            ->get();

        return response()->json([
            'labels' => $data->pluck('report_type'),
            'counts' => $data->pluck('count'),
        ]);
    }

    // Issues growth trend (last 14 days)
    public function getIssueGrowth()
    {
        $fromDate = now()->subDays(13)->startOfDay();
        $data = Issue::where('created_at', '>=', $fromDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $counts = [];

        for ($i = 0; $i < 14; $i++) {
            $date = now()->subDays(13 - $i)->toDateString();
            $labels[] = $date;
            $counts[] = $data->firstWhere('date', $date)->count ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'counts' => $counts,
        ]);
    }

    

    // Issues grouped by status
public function getIssuesByStatus()
{
    $statuses = ['Pending', 'In Review', 'In Progress', 'Fixed'];

    $data = Issue::select('status', DB::raw('COUNT(*) as count'))
        ->whereIn('status', $statuses)
        ->groupBy('status')
        ->pluck('count','status');

    $counts = [];
    foreach ($statuses as $status) {
        $counts[] = $data[$status] ?? 0;
    }

    return response()->json([
        'labels' => $statuses,
        'counts' => $counts,
    ]);
}


    /**
     * Fetch issues with optional search, filter, and pagination.
     */
    public function index(Request $request)
    {
        $query = Issue::query();


        // Search by heading, description, district, or location
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('heading', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $limit = $request->query('limit', 10);

        $issues = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'issues' => $issues->items(),
            'current_page' => $issues->currentPage(),
            'last_page' => $issues->lastPage(),
            'total' => $issues->total(),
        ]);
    }

    /**
     * Fetch a single issue by ID.
     */
    public function show($id)
    {
        $issue = Issue::find($id);

        if (!$issue) {
            return response()->json(['status' => 'error', 'message' => 'Issue not found']);
        }

        return response()->json([
            'status' => 'success',
            'issue' => $issue,
        ]);
    }

    /**
     * Update the status of an issue.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Review,In Progress,Fixed',
        ]);

        $issue = Issue::find($id);

        if (!$issue) {
            return response()->json(['success' => false, 'message' => 'Issue not found']);
        }

        $issue->status = $request->status;
        $issue->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
