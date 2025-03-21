<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echo Layout</title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-200 h-screen flex">
<!-- Sidebar -->
<aside class="w-1/6 border-gray-200 bg-gray-300 p-12 shadow-lg">
    <div class="flex place-content-center h-1/6">
        <a href="/" class="font-sans font-bold text-5xl text-fuchsia-900">echo</a>
    </div>

    <!-- Comes from Providers/ViewServiceProvider -->
    <ul>
        <li class="mb-2">
            <a href="{{ route('popular') }}">Popular</a>
        </li>
        <li class="mb-2">
            <h2>Most popular hubs</h2>
        </li>
        @forelse($sidebarHubs as $hub)
            <li class="mb-2">
                <a href="{{ route('hubs.show', $hub->slug) }}" class="text-gray-700 hover:text-black"> {{ $hub->name }}</a>
            </li>
        @empty
        @endforelse
        <li class="mb-2">
            <a href="{{ route('hubs.index') }}">See all hubs</a>
        </li>
    </ul>

</aside>

<!-- Main Content -->
<main class="flex-1 pt-16 pl-12 pr-12 relative h-full">
    <!-- Top Bar -->
    <div class="flex place-content-center items-center mb-16 ">
        <!-- Search Bar -->
        <input type="text" placeholder="Search..." class="w-2/5 p-2 border rounded-full ">
    </div>
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')

</main>
</body>
</html>
