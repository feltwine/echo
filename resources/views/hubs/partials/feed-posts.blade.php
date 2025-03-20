@foreach($feedPosts as $post)
    <div class="h-full w-[90%]">
        <div class="w-full mt-3 mb-3 cursor-pointer"
             onclick="window.location='{{ route('hubs.show', ['slug' => $post->hub->slug, 'postSlug' => $post->slug]) }}'">
            <h2 class="text-xl font-semibold">
                {{ $post->title }}
            </h2>
            <h3 class="mt-1 text-sm text-gray-600 flex items-center">
                <a href="{{ route('users.show', $post->user->user_name) }}" class="hover:underline">
                    {{ $post->user->user_name }}
                </a>
            </h3>
            <p class="text-gray-700 mt-2">{{ Str::limit($post->body, 150) }}</p>

            <div class="flex w-full justify-between items-center mt-3">
                <div class="flex w-full items-center justify-between space-x-2">
                    <div class="flex items-center space-x-2">
                        <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">▲</button>
                        <p class="font-medium">{{ $post->vote_count }}</p>
                        <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">▼</button>
                    </div>

                    <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">{{$post->comment_count}}
                        Comments
                    </button>
                </div>
            </div>
        </div>
        <hr class="h-px border-0 bg-fuchsia-900">
    </div>
@endforeach
