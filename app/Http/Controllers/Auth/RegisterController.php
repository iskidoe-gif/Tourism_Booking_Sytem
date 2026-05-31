<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Show registration form
    public function showForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            // password_confirmation field is automatically checked by 'confirmed'
        ]);

        // Create user — password is auto-hashed via User model's $casts
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']), // explicit hash
            'role'     => 'tourist', // default role
        ]);

        // Log the user in right after registering
        Auth::login($user);

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        return redirect()->route('packages.index')
            ->with('success', 'Welcome, ' . $user->name . '! Your account has been created.');
    }
}
