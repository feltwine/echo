@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4">
                            <div class="mb-4 text-center">
                                <img src="{{ $user->userProfile->avatar_path ? asset('storage/' . $user->userProfile->avatar_path) : asset('images/default-avatar.png') }}"
                                     alt="{{ $user->user_name }}"
                                     class="w-24 h-24 rounded-full mx-auto mb-3 object-cover">
                                <h2 class="text-xl font-bold">{{ $user->userProfile->display_name }}</h2>
                                <p class="text-gray-600">@{{ $user->user_name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <div class="text-center p-2 bg-gray-50 rounded">
                                    <div class="font-bold text-lg">{{ $stats['followers_count'] }}</div>
                                    <div class="text-sm text-gray-600">Followers</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 rounded">
                                    <div class="font-bold text-lg">{{ $stats['followings_count'] }}</div>
                                    <div class="text-sm text-gray-600">Following</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 rounded">
                                    <div class="font-bold text-lg">{{ $stats['post_count'] }}</div>
                                    <div class="text-sm text-gray-600">Posts</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 rounded">
                                    <div class="font-bold text-lg">{{ $stats['comment_count'] }}</div>
                                    <div class="text-sm text-gray-600">Comments</div>
                                </div>
                            </div>

                            @if($user->userProfile->bio)
                                <div class="mb-4">
                                    <h3 class="font-semibold text-gray-800 mb-2">Bio</h3>
                                    <p class="text-gray-600 text-sm">{{ $user->userProfile->bio }}</p>
                                </div>
                            @endif

                            <div class="mt-4">
                                <a href="{{ route('profile.edit') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                                    Edit Profile
                                </a>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 border-t">
                            <h3 class="font-semibold text-gray-800 mb-2">Quick Links</h3>
                            <nav class="space-y-1">
                                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md bg-blue-50 text-blue-700 font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.posts') }}" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                                    My Posts
                                </a>
                                <a href="{{ route('profile.comments') }}" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                                    My Comments
                                </a>
                                <a href="{{ route('profile.followed-hubs') }}" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                                    Followed Hubs
                                </a>
                                <a href="{{ route('profile.saved-posts') }}" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                                    Saved Posts
                                </a>
                                <a href="{{ route('profile.settings') }}" class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100">
                                    Account Settings
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="md:col-span-3">
                    <!-- Status Message -->
                    @if (session('status'))
                        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded mb-6">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Feed Section -->
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-lg font-bold">Your Feed</h2>
                        </div>

                        <div class="divide-y">
                            @forelse($feedPosts as $post)
                                <div class="p-6">
                                    <div class="flex items-center mb-4">
                                        <img src="{{ $post->user->userProfile->avatar_path ? asset('storage/' . $post->user->userProfile->avatar_path) : asset('images/default-avatar.png') }}"
                                             alt="{{ $post->user->user_name }}"
                                             class="w-10 h-10 rounded-full mr-3 object-cover">
                                        <div>
                                            <p class="font-medium">{{ $post->user->userProfile->display_name }}</p>
                                            <p class="text-sm text-gray-500">
                                                Posted in
                                                <a href="{{ route('hubs.show', $post->hub->slug) }}" class="text-blue-600 hover:underline">
                                                    {{ $post->hub->name }}
                                                </a>
                                                • {{ $post->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-bold mb-2">{{ $post->title }}</h3>

                                    <div class="text-gray-800 mb-4">
                                        {{ Str::limit($post->content, 200) }}
                                    </div>

                                    <div class="flex space-x-4">
                                        <a href="#" class="text-gray-500 hover:text-blue-600 flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                            </svg>
                                            {{ $post->votes->where('vote', 1)->count() }}
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-red-600 flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13v-5m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                                            </svg>
                                            {{ $post->votes->where('vote', -1)->count() }}
                                        </a>
                                        <a href="#" class="text-gray-500 hover:text-gray-800 flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                            </svg>
                                            {{ $post->comments_count ?? 0 }}
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-500">
                                    No posts to display. Follow more hubs or users to see content in your feed.
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t">
                            {{ $feedPosts->links() }}
                        </div>
                    </div>

                    <!-- Followed Hubs -->
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-lg font-bold">Your Hubs</h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                @forelse($user->followedHubs as $hub)
                                    <a href="{{ route('hubs.show', $hub->slug) }}" class="block group">
                                        <div class="bg-gray-100 rounded-lg p-4 text-center transition hover:bg-gray-200">
                                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white mx-auto mb-2">
                                                {{ strtoupper(substr($hub->name, 0, 1)) }}
                                            </div>
                                            <h3 class="font-medium group-hover:text-blue-600 truncate">{{ $hub->name }}</h3>
                                        </div>
                                    </a>
                                @empty
                                    <div class="col-span-full text-center text-gray-500">
                                        You haven't followed any hubs yet.
                                        <a href="{{ route('hubs.index') }}" class="text-blue-600 hover:underline">Discover hubs</a>
                                    </div>
                                @endforelse
                            </div>

                            @if($user->followedHubs->count() > 0)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('profile.followed-hubs') }}" class="text-blue-600 hover:underline">View all hubs</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Following Users -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b">
                            <h2 class="text-lg font-bold">People You Follow</h2>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @forelse($user->following as $followedUser)
                                    <a href="{{ route('users.show', $followedUser->user_name) }}" class="block group">
                                        <div class="flex flex-col items-center p-4 rounded-lg transition hover:bg-gray-50">
                                            <img src="{{ $followedUser->userProfile->avatar_path ? asset('storage/' . $followedUser->userProfile->avatar_path) : asset('images/default-avatar.png') }}"
                                                 alt="{{ $followedUser->user_name }}"
                                                 class="w-16 h-16 rounded-full mb-2 object-cover">
                                            <h3 class="font-medium group-hover:text-blue-600">{{ $followedUser->userProfile->display_name }}</h3>
                                            <p class="text-sm text-gray-500">@{{ $followedUser->user_name }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="col-span-full text-center text-gray-500">
                                        You aren't following anyone yet.
                                        <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline">Discover users</a>
                                    </div>
                                @endforelse
                            </div>

                            @if($user->following->count() > 0)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('users.following', $user->user_name) }}" class="text-blue-600 hover:underline">View all followed users</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
