@foreach($feedPosts as $post)
    <div class="w-3/5 bg-gray-300 p-4 shadow-md mb-4 cursor-pointer"
         onclick="window.location='{{ route('hubs.posts.show', ['slug' => $post->hub->slug, 'postSlug' => $post->slug]) }}'"
        <h2 class="text-xl font-semibold">
            {{ $post->title }}
        </h2>
        <h3 class="mt-1 text-sm text-gray-600 flex items-center">
            <a href="{{ route('users.show', $post->user->user_name) }}" class="hover:underline">
                {{ $post->user->user_name }}
            </a>
        </h3>
        <p class="text-gray-700 mt-2">{{ Str::limit($post->body, 150) }}</p>

        <div class="flex justify-between items-center mt-3">
            <div class="flex items-center space-x-2">
                <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">▲</button>
                <p class="font-medium">{{ $post->vote_count }}</p>
                <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">▼</button>
                <button class="px-2 py-1 bg-gray-400 hover:bg-gray-500 text-white rounded">{{$post->comment_count}} Comments</button>
            </div>
        </div>
    </div>
@endforeach
