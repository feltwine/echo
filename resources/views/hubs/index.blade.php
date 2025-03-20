@extends('layouts.app')

@section('scripts')
    @vite('resources/js/scroll.js')
@endsection

@section('content')
    <div class="content-container">
        <!-- Scrollable hubs container with fixed height -->
        <div id="postFeedContainer" class="w-full overflow-y-auto" style="height: 85vh; position: relative;">
            <div class="w-full flex place-items-center flex-col pt-8 h-full">
                <div id="postFeed" class="w-full flex place-items-center flex-col">
                    @if($hubs->isNotEmpty())
                        @foreach($hubs as $hub)
                            <div class="w-3/5 bg-gray-300 p-4 shadow-md mb-4 cursor-pointer"
                                 onclick="window.location='{{ route('hubs.show', ['slug' => $hub->slug]) }}'">
                                <h2>{{$hub->name}}</h2>
                                <p>{{$hub->description}}</p>
                                <p>
                                    {{$hub->followers_count}} followers,
                                    @php
                                        $age = $hub->created_at->diff(now());
                                    @endphp

                                    @if($age->y > 0)Community for {{ $age->y }} years
                                    @elseif($age->m > 0)Community for {{ $age->m }} months
                                    @elseif($age->d > 0)Community for {{ $age->d }} days
                                    @else Started today @endif
                                </p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-600">No hubs available.</p>
                    @endif
                </div>
                <div id="loadMoreButtonContainer" style="text-align: center; margin-top: 20px;">
                    <button id="loadMoreButton" class="px-4 py-2 bg-blue-600 text-white">Load More</button>
                </div>
                <!-- Loading indicator inside the scrollable area -->
                <div id="loading" style="display: none; text-align: center;">Loading more hubs...</div>
            </div>
        </div>
    </div>
@endsection
