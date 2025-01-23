<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Request;

class UserProfileController extends Controller
{
    public function updateAvatar(Request $request, User $user)
    {
        $request->validate([
           'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,png,jpeg']
        ]);

        if ($request->hasFile('avatar')) {
            // For prototype development, avatar is stored in a folder storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->userProfile()->update(['avatar' => $path]);
        }

        return back()->with('success', 'Avatar updated successfully');
    }

    public function updateBackground(Request $request, User $user)
    {
        $request->validate([
            'background' => ['required', 'image', 'max:2048', 'mimes:jpg,png,jpeg']
        ]);

        if ($request->hasFile('background')) {
            $path = $request->file('background')->store('backgrounds', 'public');
            $user->userProfile->update(['background' => $path]);
        }

        return back()->with('success', 'Background updated successfully');
    }

    public function updateBackgroundColor(Request $request, User $user)
    {
        $request->validate([
            'background_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}[A-Fa-f0-9]{3}$/i']
        ]);

        $user->userProfile->update([
            'background_color' => $request->background_color
        ]);

        return back()->with('success', 'Background updated successfully');
    }
}
