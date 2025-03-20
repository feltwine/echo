@extends('layouts.app')

@section('scripts')
    @vite('resources/js/scroll.js')
@endsection

@section('content')
    <div class="h-full w-full place-items-center flex flex-col" style="height: 78vh;">
        <div class="w-2/3 bg-gray-300 rounded-t-xl flex flex-col mx-auto flex-grow">
            <!-- Reddit like filter menu -->
            <div class="px-4 md:px-6 pt-6 pb-6">
                <div class="flex items-center">
                    <div class="relative inline-block text-left">
                        <button id="sortDropdown" type="button"
                                class="rounded-md inline-flex justify-between items-center w-40 px-4 py-2 text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium shadow-sm border border-gray-200"
                                onclick="toggleDropdown('sortOptions')">
                            <span id="currentSort" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-fuchsia-700"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                </svg>
                                @if(request()->get('sort') == 'new')
                                    New
                                @elseif(request()->get('sort') == 'controversial')
                                    Controversial
                                @else
                                    Popular
                                @endif
                            </span>
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                      clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <div id="sortOptions"
                             class="hidden origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <a href="?sort=popular"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ (request()->get('sort') == 'popular' || !request()->has('sort')) ? 'bg-fuchsia-50 text-fuchsia-700' : '' }}">
                                    Popular
                                </a>
                                <a href="?sort=new"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->get('sort') == 'new' ? 'bg-fuchsia-50 text-fuchsia-700' : '' }}">
                                    New
                                </a>
                                <a href="?sort=controversial"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->get('sort') == 'controversial' ? 'bg-fuchsia-50 text-fuchsia-700' : '' }}">
                                    Controversial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="h-px border-0 bg-fuchsia-800 opacity-50 mx-4 md:mx-6">
            <div id="postContainer" class="w-full flex place-items-center flex-col overflow-y-auto"
                 style="height: 70vh;position: relative;">
                <div id="postFeed" class="w-full flex place-items-center flex-col">
                    @if($feedPosts->isNotEmpty())
                        @include('home.partials.feed-posts', ['feedPosts' => $feedPosts])
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="mt-4 text-lg text-gray-500">No posts available in this hub yet.</p>
                        </div>
                    @endif
                </div>
                <div id="loadMoreButtonContainer" style="text-align: center; margin-top: 20px;">
                    <button id="loadMoreButton" class="px-4 py-2 bg-fuchsia-900 text-white rounded-md">Load More
                    </button>
                </div>
                <!-- Loading indicator inside the scrollable area -->
                <div id="loading" class="py-6 text-center hidden">
                    <div
                        class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-fuchsia-700 border-t-transparent"></div>
                    <p class="mt-2 text-gray-600">Loading more posts...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.scrollTo(0, 0);
            document.getElementById('postContainer').scrollTop = 0; // Reset scroll inside postFeed container
        };

        // Toggle dropdown visibility
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            if (!e.target.closest('#sortDropdown')) {
                document.getElementById('sortOptions').classList.add('hidden');
            }
        });

    </script>
@endsection
