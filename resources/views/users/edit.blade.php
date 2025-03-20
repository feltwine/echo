<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ __('Edit Profile') }}
                    </h2>

                    @if (session('status'))
                        <div class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 p-4 rounded-md mb-6">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 p-4 rounded-md mb-6">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Account Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Account Information') }}
                            </h3>

                            <div class="mb-4">
                                <label for="user_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Username') }}
                                </label>
                                <input type="text" name="user_name" id="user_name" value="{{ old('user_name', $user->user_name) }}"
                                       class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Email Address') }}
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                       class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Either email or phone number is required.') }}
                                    @if($user->email && $user->email_verified_at)
                                        <span class="text-green-600 dark:text-green-400 ml-1">{{ __('(Verified)') }}</span>
                                    @elseif($user->email)
                                        <span class="text-orange-600 dark:text-orange-400 ml-1">{{ __('(Unverified)') }}</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Phone Number') }}
                                </label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                @if($user->phone && $user->phone_verified_at)
                                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                                        {{ __('Verified') }}
                                    </p>
                                @elseif($user->phone)
                                    <p class="mt-1 text-sm text-orange-600 dark:text-orange-400">
                                        {{ __('Unverified') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('Profile Information') }}
                            </h3>

                            <div class="mb-4">
                                <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Display Name') }}
                                </label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $user->userProfile->display_name) }}"
                                       class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                       required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('First Name') }}
                                    </label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->userProfile->first_name) }}"
                                           class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Last Name') }}
                                    </label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->userProfile->last_name) }}"
                                           class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Bio') }}
                                </label>
                                <textarea name="bio" id="bio" rows="3"
                                          class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $user->userProfile->bio) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Date of Birth') }}
                                    </label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->userProfile->date_of_birth) }}"
                                           class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Gender') }}
                                    </label>
                                    <select name="gender" id="gender"
                                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('Prefer not to say') }}</option>
                                        <option value="male" {{ old('gender', $user->userProfile->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                        <option value="female" {{ old('gender', $user->userProfile->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                        <option value="other" {{ old('gender', $user->userProfile->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                    </select>
