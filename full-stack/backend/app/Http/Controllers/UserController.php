<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    
        // Check if a user with this email already exists
        $existingUser = User::where('email', $validatedData['email'])->first();
    
        if ($existingUser) {
            // User with the same email exists, update the user's data
            $existingUser->name = $validatedData['name'];
            $existingUser->password = Hash::make($validatedData['password']);
            $existingUser->is_guest = false; 
            $existingUser->save();
    
            // Authenticate the user
            Auth::login($existingUser);
    
            // Generate and return an API token for the user
            $token = $existingUser->createToken('api-token')->plainTextToken;
    
            return response()->json([
                'message' => 'User data updated and authenticated',
                'user' => $existingUser->only(['id', 'name']),
                'token' => $token,
            ], 200);
        } else {
            // User with this email doesn't exist, create a new user
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->is_guest = false;
            $user->save();
    
            // Authenticate the new user
            Auth::login($user);
    
            // Generate and return an API token for the new user
            $token = $user->createToken('api-token')->plainTextToken;
    
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user->only(['id', 'name']),
                'token' => $token,
            ], 201);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user->only(['id', 'name']),
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Login failed'], 401);
    }
}
