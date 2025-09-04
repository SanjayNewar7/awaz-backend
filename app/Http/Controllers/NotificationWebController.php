<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Issue;
use App\Models\User;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationWebController extends Controller
{
    public function index()
    {
        $issues = Issue::select('id', 'heading', 'district', 'location', 'ward')->latest()->get();
        $notifications = SystemNotification::latest()->take(50)->get();

        return view('notification', compact('issues', 'notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'target_type' => 'required|in:all,issue_owner,engaged,issue_location',
            'issue_id'    => 'nullable|exists:issues,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('notifications', 'public');
        }

        $recipients = collect();

        switch ($request->target_type) {
            case 'all':
                // All users
                $recipients = User::pluck('user_id');
                break;

            case 'issue_owner':
                if ($request->issue_id) {
                    $issue = Issue::find($request->issue_id);
                    if ($issue) {
                        $recipients = collect([$issue->user_id]);
                    }
                }
                break;

            case 'engaged':
                if ($request->issue_id) {
                    $commenters = DB::table('issue_comments')
                        ->where('issue_id', $request->issue_id)
                        ->pluck('user_id');

                    $reactors = DB::table('issue_reactions')
                        ->where('issue_id', $request->issue_id)
                        ->pluck('user_id');

                    $recipients = $commenters->merge($reactors)->unique();
                }
                break;

            case 'issue_location':
                if ($request->issue_id) {
                    $issue = Issue::find($request->issue_id);
                    if ($issue) {
                        // Compare first word of location with users.city ignoring case
                        $issueCityFirstWord = Str::lower(explode(' ', $issue->location)[0]);

                        $recipients = User::where('district', $issue->district)
                            ->where('ward', $issue->ward)
                            ->where(DB::raw('LOWER(city)'), $issueCityFirstWord)
                            ->pluck('user_id');
                    }
                }
                break;
        }

        // Insert system notifications for each recipient
        foreach ($recipients as $userId) {
            SystemNotification::create([
                'user_id'   => $userId,
                'title'     => $request->title,
                'message'   => $request->message,
                'issue_id'  => $request->issue_id,
                'image'     => $path,
                'district'  => $request->issue_id ? Issue::find($request->issue_id)->district : null,
                'ward'      => $request->issue_id ? Issue::find($request->issue_id)->ward : null,
                'area_name' => $request->issue_id ? Issue::find($request->issue_id)->location : null,
            ]);
        }

        return redirect()->route('notifications.index')->with('success', 'Notification sent successfully.');
    }

    public function indexApi()
    {
        $user = Auth::user();
        $notifications = SystemNotification::where('user_id', $user->user_id)
            ->with('issue:id,heading,status')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a specific system notification as read (API).
     */
    public function markSystemNotificationAsRead($id)
    {
        $notification = SystemNotification::findOrFail($id);

        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all system notifications as read for the authenticated user (API).
     */
    public function markAllSystemNotificationsAsRead()
    {
        SystemNotification::where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
