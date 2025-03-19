<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user()->load([
            'userProfile',
            'posts' => function ($query) {
                $query->latest()->take(5);
            },
            'followedHubs' => function ($query) {
                $query->take(10);
            },
            'following' => function ($query) {
                $query->with('userProfile')->take(10);
            }
        ]);

        $followedHubIds = $user->followedHubs->pluck('id');
        $followedUserIds = $user->following->pluck('id');

        $feedPosts = Post::where(function ($query) use ($followedHubIds, $followedUserIds) {
            $query->whereIn('hub_id', $followedHubIds)
                ->orWhereIn('user_id', $followedUserIds);
        })
            ->with(['user.userProfile', 'hub'])
            ->latest()
            ->paginate(15);

        $stats = [
            'post_count' =>$user->posts()->count(),
            'comment_count' =>$user->comments()->count(),
            'followers_count' =>$user->followers()->count(),
            'followings_count' =>$user->following()->count(),
        ];

        return view('profile.dashboard', compact('user', 'feedPosts', 'stats'));
    }

    public function posts()
    {
        $user = Auth::user();
        $posts = $user->posts()
            ->with(['hub', 'votes'])
            ->latest()
            ->paginate(20);

        return view('profile.posts', compact( 'posts'));
    }

    public function comments()
    {
        $user = Auth::user();
        $comments = $user->comments()
            ->with(['post.user', 'post.hub'])
            ->latest()
            ->paginate(20);

        return view('profile.comments', compact('comments'));
    }

    public function followedHubs()
    {
        $user = Auth::user();
        $hubs = $user->followedHubs()
            ->withCount('followers')
            ->paginate(20);

        return view('profile.followed-Hubs', compact('hubs'));
    }

    public function savedPosts()
    {
        $user = Auth::user();
        $posts = $user->followedPosts()
            ->with(['user.userProfile', 'hub'])
            ->latest('post_followers.created_at')
            ->paginate(20);

        return view('profile.saved-posts', compact('posts'));
    }

    public function settings()
    {
        $user = Auth::user()->load('userProfile');
        return view('profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            Auth::logoutOtherDevices($request->password);
        }

        return back()->with('success', 'Account settings have been changed.');
    }

    // Soft deletes
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $user = Auth::user();

        $user->delete();
        $user->userProfile->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
