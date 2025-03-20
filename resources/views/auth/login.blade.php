@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="max-w-md w-full px-6 py-8 bg-white shadow-md rounded-lg">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">Login to Your Account</h1>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="login" class="block text-gray-700 text-sm font-bold mb-2">Email or Phone</label>
                    <input id="login" type="text" name="login" value="{{ old('login') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Email or phone number">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Your password">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-blue-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                </div>

                <div class="mb-6">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Log In
                    </button>
                </div>

                <p class="text-center text-gray-600 text-sm">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register</a>
                </p>
            </form>
        </div>
    </div>
@endsection
