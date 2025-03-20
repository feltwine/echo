@extends('layouts.app')

@section('scripts')
    @vite('resources/js/scroll.js')
@endsection

@section('content')
    <div class="h-full w-full place-items-center flex flex-col overflow-y-auto"
         style="height: 80vh; position: relative;">
        <!-- Top banner at 2/3 width -->
        <div class="w-2/3 h-32 rounded-t-xl mx-auto" style="background: {{ $hub->background_color }}"></div>

        <!-- Background container matching the top banner width -->
        <div class="w-2/3 bg-gray-300 flex flex-col mx-auto flex-grow">
            <!-- Content div that's 90% of the background container (close to original 3/5 of full width) -->
            <div class="w-[90%] mx-auto flex flex-row h-[6rem]">
                <div class="w-32 h-32 mt-[-2rem] ">
                    <img class="rounded-md"
                         src="{{ $hub->avatar_path ? asset($hub->avatar_path) : asset('avatars/dafault-hub-avatar.jpg') }}"
                         alt="Hub Avatar">
                </div>

                <div class="flex flex-col pl-4 md:flex-row justify-between w-full mt-2">
                    <div class="flex flex-col">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $hub->name }}</h1>
                        <p class="text-gray-600 mb-1">
                            by <a href="{{route('users.show', $hub->user->user_name)}}" class="text-fuchsia-700 hover:text-fuchsia-900">{{ $hub->user->userProfile->display_name }}</a>
                        </p>
                        <p class="text-gray-700 mt-1 max-w-2xl">{{ $hub->description }}</p>
                    </div>
                    <div class="flex flex-row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-fuchsia-700" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        <span class="ml-1 font-medium">{{ number_format($hub->followers_count) }} followers</span>
                    </div>
                </div>
            </div>

            <!-- Simplified Reddit-style Sort Menu -->
            <div class="w-full mx-auto flex flex-col ">
                <div class="w-[90%] mx-auto">
                    <!-- Simple sort options -->
                    <div class="flex items-center h-12 text-sm">
                        <div class="relative inline-block text-left">
                            <button id="sortDropdown" type="button"
                                    class=" rounded-md inline-flex justify-between items-center w-32 px-3 py-1.5 text-gray-700 bg-gray-100 hover:bg-gray-200 text-sm font-medium"
                                    onclick="toggleDropdown('sortOptions')">
                                <span id="currentSort">
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
                                 class="hidden origin-top-left absolute left-0 mt-2 w-32 shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                <div class="py-1">
                                    <a href="?sort=popular"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ (request()->get('sort') == 'popular' || !request()->has('sort')) ? 'bg-blue-50 text-blue-600' : '' }}">
                                        Popular
                                    </a>
                                    <a href="?sort=new"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->get('sort') == 'new' ? 'bg-blue-50 text-blue-600' : '' }}">
                                        New
                                    </a>
                                    <a href="?sort=controversial"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->get('sort') == 'controversial' ? 'bg-blue-50 text-blue-600' : '' }}">
                                        Controversial
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <div class="w-[90%] mx-auto flex flex-grow mt-4">
                <!-- Your posts will go here -->
            </div>
        </div>
    </div>

    <script>
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
