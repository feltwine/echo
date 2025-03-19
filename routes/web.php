<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\HubController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('email/verify', [AuthController::class, 'verifyEmail'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');
});

/*
|--------------------------------------------------------------------------
| User Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // User profile management
    Route::get('profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [UserController::class, 'update'])->name('profile.update');

    // User dashboard
    Route::get('dashboard', [UserProfileController::class, 'dashboard'])->name('dashboard');

    // User settings
    Route::get('profile/settings', [UserProfileController::class, 'settings'])->name('profile.settings');
    Route::put('profile/settings', [UserProfileController::class, 'updateSettings'])->name('profile.update-settings');
    Route::delete('profile/delete', [UserProfileController::class, 'deleteAccount'])->name('profile.delete');
});

/*
|--------------------------------------------------------------------------
| Public User Routes
|--------------------------------------------------------------------------
*/
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::get('users/{username}', [UserController::class, 'show'])->name('users.show');

/*
|--------------------------------------------------------------------------
| Public Hub Routes
|--------------------------------------------------------------------------
*/
Route::get('hubs', [HubController::class, 'index'])->name('hubs.index'); // List all hubs
Route::get('hubs/{slug}', [HubController::class, 'show'])->name('hubs.show'); // View a single hub by slug

/*
|--------------------------------------------------------------------------
| Authenticated Hub Routes (Create, Update, Delete)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('hubs/create', [HubController::class, 'create'])->name('hubs.create'); // Show create form
    Route::post('hubs', [HubController::class, 'store'])->name('hubs.store'); // Store new hub

    Route::get('hubs/{slug}/edit', [HubController::class, 'edit'])->name('hubs.edit'); // Edit form
    Route::put('hubs/{slug}', [HubController::class, 'update'])->name('hubs.update'); // Update hub

    Route::delete('hubs/{slug}', [HubController::class, 'destroy'])->name('hubs.destroy'); // Delete hub
});

/*
|--------------------------------------------------------------------------
| Posts Nested Under Hubs
|--------------------------------------------------------------------------
*/
Route::get('hubs/{slug}/posts', [PostController::class, 'index'])->name('hubs.posts.index'); // List posts in a hub
Route::get('hubs/{slug}/posts/{postSlug}', [PostController::class, 'show'])->name('hubs.posts.show'); // View a post in a hub

Route::middleware('auth')->group(function () {
    Route::get('hubs/{slug}/posts/create', [PostController::class, 'create'])->name('hubs.posts.create'); // Create a new post under a hub
    Route::post('hubs/{slug}/posts', [PostController::class, 'store'])->name('hubs.posts.store'); // Store new post under a hub

    Route::get('hubs/{slug}/posts/{postSlug}/edit', [PostController::class, 'edit'])->name('hubs.posts.edit'); // Edit post in a hub
    Route::put('hubs/{slug}/posts/{postSlug}', [PostController::class, 'update'])->name('hubs.posts.update'); // Update post in a hub

    Route::delete('hubs/{slug}/posts/{postSlug}', [PostController::class, 'destroy'])->name('hubs.posts.destroy'); // Delete post in a hub
});
