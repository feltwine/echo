@extends('layouts.app')

@section('scripts')
    @vite('resources/js/scroll.js')
@endsection

@section('content')
    <div class="content-container">
        <!-- Fixed header content if needed -->

        <!-- Scrollable posts container with fixed height -->
        <div id="postFeedContainer" class="w-full overflow-y-auto" style="height: 85vh; position: relative;">
            <div class="w-full flex place-items-center flex-col pt-8 h-full">
                <div id="postFeed" class="w-full flex place-items-center flex-col">
                    @if($feedPosts->isNotEmpty())
                        @include('home.partials.feed-posts', ['feedPosts' => $feedPosts])
                    @else
                        <p class="text-gray-600">No posts available.</p>
                    @endif
                </div>
                <div id="loadMoreButtonContainer" style="text-align: center; margin-top: 20px;">
                    <button id="loadMoreButton" class="px-4 py-2 bg-blue-600 text-white">Load More</button>
                </div>
                <!-- Loading indicator inside the scrollable area -->
                <div id="loading" style="display: none; text-align: center;">Loading more posts...</div>
            </div>
        </div>
    </div>
@endsection
