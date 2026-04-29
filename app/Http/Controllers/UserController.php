<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Pet;

class UserController extends Controller
{
    //Registration Page

    public function showRegister() {
        return view('authentication.register');
    }

    public function register(Request $request) {
    
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    // ================================================================

    // Login Page

    public function showLogin() {
        return view('authentication.login');
    }

    public function login(Request $request) {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('petlover.dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.'
        ]);
    }

    public function logout(Request $request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login');
    }

    // ================================================================

    // About Me Page
    // About Me Page
    public function aboutMe() {
        $user = Auth::user();
        
        // 1. Fetch the pets using the relationship defined in your User model
        $myPets = $user->pets; 

        // 2. Pass BOTH 'user' and 'myPets' to the view
        return view('shared.about-me', compact('user', 'myPets'));
    }
   
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // ========================
        // VALIDATION
        // ========================
        $request->validate([
            'first_name'      => 'required|string|max:50',
            'last_name'       => 'required|string|max:50',
            'contact_number'  => 'required|string|max:20',
            'address'         => 'nullable|string|max:255',
            'birthdate'       => 'nullable|date',
            'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ========================
        // PROFILE IMAGE UPLOAD
        // ========================
        if ($request->hasFile('profile_image')) {

            // delete old image safely
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // store new image in "profiles" folder
            $path = $request->file('profile_image')->store('profiles', 'public');

            $user->profile_image = $path;
            $user->save(); // persist image change before the update() call below
        }

        // ========================
        // UPDATE USER FIELDS
        // ========================
        $user->update([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'contact_number'  => $request->contact_number,
            'address'         => $request->address,
            'birthdate'       => $request->birthdate,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

}