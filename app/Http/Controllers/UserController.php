<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('userProfile')
            ->when($request->search, function ($query, $search) {
                return $query->where('user_name', 'like', '%{$search}%')
                    ->orWhere('userProfile', function ($q) use ($search) {
                        $q->where('display_name', 'like', '%{$search}%')
                            ->orWhere('first_name', 'like', '%{$search}%')
                            ->orWhere('last_name', 'like', '%{$search}%');
                    });
            })
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function show($username)
    {
        $user = User::where('user_name', $username)
            ->with(['userProfile', 'posts' => function ($query) {
                $query->latest()->take(10);
            }, 'followers', 'following'])
            ->firstOrFail();

        $isFollowing = Auth::check() ? Auth::user()->following->contains($user->id) : false;

        return view('users.show', compact('user', 'isFollowing'));
    }

    public function edit()
    {
        $user = Auth::user()->load('userProfile');
        return view('users.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'user_name' => ['required|string|max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required_without:phone|email|max:255|', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required_without:email|string|max:255', Rule::unique('users')->ignore($user->id)],
            'display_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date|before:today',
            'gander' => 'nullable|enum(male,female, other)',
            'avatar_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_color' => 'nullable|string|regex:/^#)[0-9a-fA-F]{6}|[A-Fa-f0-9]{3}$/',
            ]);

        $user->update([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        if ($user->wasChanged('email') && $request->email) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        };

        $profileData = $request->only([
            'display_name',
            'first_name',
            'last_name',
            'bio',
            'date_of_birth',
            'gender',
            'background_color'
        ]);

        if ($request->hasFile('avatar_path')) {
            if ($user->userProfile->avatar_path) {
                Storage::disk('public')->delete($user->userProfile->avatar_path);
            }

            $path = $request->file('avatar_path')->store('avatars', 'public');
            $profileData['avatar_path'] = $path;
        }

        if ($request->hasFile('background_path')) {
            if ($user->userProfile->background_path) {
                Storage::disk('public')->delete($user->userProfile->background_path);
            }

            $path = $request->file('background_path')->store('backgrounds', 'public');
            $profileData['background_path'] = $path;
        }

        $user->userProfile()->update($profileData);

        return redirect()->route('users.show', $user->user_name)
            ->with('success', 'Profile updated successfully.');
    }

    public function follow($username)
    {
        $userToFollow = User::where('user_name', $username)->firstOrFail();
        $user = Auth::user();

        if ($user->id === $userToFollow->id) {
            return back()->with('info', 'You cannot follow yourself.');
        }

        $user->following()->attach($userToFollow->id);
        $userToFollow->userProfile->increment('followers_count');

        return back()->with('status', 'You are now following {$UserToFollow->user_name} aka {$userToFollow->userProfile->display_name}!.');
    }

    public function unfollow($username)
    {
        $userToUnfollow = User::where('user_name', $username)->firstOrFail();
        $user = Auth::user();

        if (!$user->following->contains($userToUnfollow->id)) {
            return back()->with('info', 'You are not following this user.');
        }

        $user->following()->detach($userToUnfollow->id);
        $userToUnfollow->userProfile->decrement('followers_count');

        return back()->with('status', 'You have unfollowed {$userToUnfollow->user_name} aka {$userToUnfollow->userProfile->display_name}!}');
    }

    public function followers($username)
    {
        $user = User::where('user_name', $username)
            ->with(['followers.userProfile'])
            ->firstOrFail();

        return view('users.followers', compact('user'));
    }

    public function following($username)
    {
        $user = User::where('user_name', $username)
            ->with(['followers.userProfile'])
            ->firstOrFail();

        return view('users.following', compact('user'));
    }
}
