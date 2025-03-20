<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <!-- Background Image/Color -->
                <div class="h-40 w-full bg-cover bg-center" style="background-color: {{ $user->userProfile->background_color }};
                    @if($user->userProfile->background_path) background-image: url('{{ Storage::url($user->userProfile->background_path) }}'); @endif">
                </div>

                <div class="p-6 -mt-16 relative">
                    <!-- Avatar -->
                    <div class="flex justify-between items-end mb-4">
                        <div class="flex items-end">
                            <div class="h-24 w-24 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700">
                                @if($user->userProfile->avatar_path)
                                    <img src="{{ Storage::url($user->userProfile->avatar_path) }}" alt="{{ $user->userProfile->display_name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                        <span class="text-2xl font-bold">{{ substr($user->userProfile->display_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="ml-4">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->userProfile->display_name }}</h1>
                                <p class="text-gray-600 dark:text-gray-400">{{ $user->user_name }}</p>
                            </div>
                        </div>

                        @auth
                            @if(Auth::id() !== $user->id)
                                <!-- Follow/Unfollow Button -->
                                @if($isFollowing)
                                    <form action="{{ route('users.unfollow', $user->user_name) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-md font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            {{ __('Unfollow') }}
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('users.follow', $user->user_name) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition">
                                            {{ __('Follow') }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-md font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                    {{ __('Edit Profile') }}
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Bio -->
                    @if($user->userProfile->bio)
                        <div class="mt-4 text-gray-700 dark:text-gray-300">
                            {{ $user->userProfile->bio }}
                        </div>
                    @endif

                    <!-- User Stats -->
                    <div class="flex mt-6 space-x-6 text-sm">
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->posts->count() }}</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Posts') }}</span>
                        </div>
                        <a href="{{ route('users.followers', $user->user_name) }}" class="hover:underline">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->followers->count() }}</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Followers') }}</span>
                        </a>
                        <a href="{{ route('users.following', $user->user_name) }}" class="hover:underline">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->following->count() }}</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ __('Following') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Recent Posts') }}</h2>

                    @if($user->posts->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->posts as $post)
                                <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <a href="{{ route('hubs.show', $post->hub->slug) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                            r/{{ $post->hub->name }}
                                        </a>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>

                                    <a href="#" class="block mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $post->title }}
                                        </h3>
                                    </a>

                                    @if($post->content)
                                        <p class="text-gray-700 dark:text-gray-300 text-sm line-clamp-2">
                                            {{ $post->content }}
                                        </p>
                                    @endif

                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-3 space-x-4">
                                        <span>
                                            <i class="far fa-arrow-up"></i> {{ $post->votes->where('type', 'upvote')->count() }}
                                        </span>
                                        <span>
                                            <i class="far fa-comment"></i> {{ $post->comments_count ?? 0 }} {{ __('Comments') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-center">
                            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('View all posts by') }} {{ $user->userProfile->display_name }}
                            </a>
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <p class="text-gray-600 dark:text-gray-400">{{ __('No posts yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
