@extends('superadmin.layout')

@section('title', 'Awaz - SuperAdmin Notifications')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="container mx-auto px-4 py-10 max-w-6xl">

    <!-- Success Message -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Create Notification Card -->
    <section class="mb-10 bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 border border-indigo-100">
        <h3 class="text-3xl font-bold text-indigo-900 mb-6 tracking-tight">Create New Notification</h3>
        <form method="POST" action="{{ route('notifications.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Heading -->
            <div>
                <label for="title" class="block text-sm font-semibold text-indigo-900 mb-2">Heading</label>
                <input type="text" id="title" name="title" required
                       class="w-full px-5 py-3 bg-white/80 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-600 text-indigo-900 placeholder-indigo-400/70 shadow-sm"
                       placeholder="Enter a catchy heading">
                @error('title') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Issue Selector (Optional) -->
            <div>
                <label for="issue_id" class="block text-sm font-semibold text-indigo-900 mb-2">Select Issue (Optional)</label>
                <select id="issue_id" name="issue_id"
                        class="w-full px-5 py-3 bg-white/80 border border-indigo-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-600 text-indigo-900 font-mono">
                    <option value="">-- Select Issue --</option>
                    @foreach($issues as $issue)
                        <option value="{{ $issue->id }}">
                            {{ str_pad($issue->heading, 50) }} ------ {{ str_pad($issue->district, 15) }} ---- {{ str_pad($issue->location ?? 'N/A', 25) }} ------ {{ $issue->ward }}
                        </option>
                    @endforeach
                </select>
                @error('issue_id') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-semibold text-indigo-900 mb-2">Message</label>
                <textarea id="message" name="message" rows="5" required
                          class="w-full px-5 py-3 bg-white/80 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-600 text-indigo-900 placeholder-indigo-400/70 shadow-sm"
                          placeholder="Write your message here..."></textarea>
                @error('message') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Image Upload with Preview -->
            <div x-data="{ preview: null }">
                <label for="image" class="block text-sm font-semibold text-indigo-900 mb-2">Image (Optional)</label>
                <input type="file" id="image" name="image" @change="preview = URL.createObjectURL($event.target.files[0])"
                       class="w-full px-5 py-3 bg-white/80 border border-indigo-200 rounded-xl text-indigo-900 file:bg-indigo-600 file:text-white file:px-4 file:py-2 file:rounded-lg file:cursor-pointer hover:file:bg-indigo-700 shadow-sm">
                <template x-if="preview">
                    <img :src="preview" class="mt-3 max-h-48 rounded-lg shadow-md">
                </template>
                @error('image') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Target Audience -->
            <div>
                <label for="target_type" class="block text-sm font-semibold text-indigo-900 mb-2">Target Audience</label>
                <select id="target_type" name="target_type" required
                        class="w-full px-5 py-3 bg-white/80 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-600 text-indigo-900 shadow-sm">
                    <option value="all">All Users</option>
                    <option value="issue_owner">Issue Registered User</option>
                    <option value="engaged">Engaged Users on Issue</option>
                    <option value="issue_location">Users from Issue’s Location</option>
                </select>
                @error('target_type') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-indigo-900 text-white font-semibold rounded-xl hover:bg-indigo-800 transition-all shadow-md">
                Send Notification
            </button>
        </form>
    </section>

    <!-- Recent Notifications Table -->
    <section class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 border border-indigo-100">
        <h3 class="text-3xl font-bold text-indigo-900 mb-6 tracking-tight">Recent Notifications</h3>
        @if(!empty($notifications) && $notifications->count())
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white sticky top-0 shadow-sm">
                            <th class="py-4 px-6 text-left text-sm font-semibold">ID</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold">Heading</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold">Message</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold">Target</th>
                            <th class="py-4 px-6 text-left text-sm font-semibold">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notif)
                            <tr class="hover:bg-indigo-200/50 transition-all border-b border-indigo-100/50">
                                <td class="py-4 px-6 text-indigo-900">{{ $notif->id }}</td>
                                <td class="py-4 px-6 text-indigo-900">{{ $notif->title }}</td>
                                <td class="py-4 px-6 text-indigo-900">{{ \Illuminate\Support\Str::limit($notif->message, 50) }}</td>
                                <td class="py-4 px-6 text-indigo-900">{{ $notif->target_type ?? '-' }}</td>
                                <td class="py-4 px-6 text-indigo-900">{{ $notif->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center py-8 text-indigo-600/80">No notifications found.</p>
        @endif
    </section>
</div>
@endsection
