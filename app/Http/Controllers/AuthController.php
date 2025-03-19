<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate the user input
        $request->validate([
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required_without:phone|email|max:255|unique:users',
            'phone' => 'required_without:email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create new user
        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email ?? null,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'email_verified_at' => null, // For email verification
        ]);

        // Create new blank user profile
        $user->userProfile()->create([
            'display_name' => $request->user_name,
            'first_name' => null,
            'last_name' => null,
            'bio' => null,
            'date_of_birth' => null,
            'gender' => null,
            'avatar_path' => null,
            'background_path' => null,
            'background_color' => '#FFFFFF',
        ]);

        // Log the user in
        Auth::login($user);

        // Send email verification if email provided
        if ($request->email) {
            $user->sendEmailVerificationNotification();
            return redirect('home')->with('status', 'Registration successful! Please verify your email address.');
        }

        return redirect('home')->with('status', 'Registration successful!');
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Email or Phone
            'password' => 'required|string',
            'remember' => 'boolean', // Add remember me checkbox
        ]);

        // Check if the input is an email or phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Attempt login with remember me functionality
        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('home')->with('status', 'Welcome back!');
        }

        // If login fails
        return back()->withErrors([
            'login' => 'The provided credentials did not match.'
        ])->withInput($request->except('password'));
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('home')->with('status', 'You have been logged out.');
    }

    // Email verification
    public function verifyEmail(Request $request)
    {
        $request->user()->markEmailAsVerified();
        return redirect('home')->with('status', 'Your email has been verified!');
    }

    // Resend email verification
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('home')->with('status', 'Your email is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    }
}
