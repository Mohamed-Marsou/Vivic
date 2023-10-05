<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            // Check if a user with this email already exists
            $existingUser = User::where('email', $validatedData['email'])->first();

            if ($existingUser) {
                if ($existingUser->is_guest) {
                    // User with the same email is a guest, update the user's data
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
                    // User with this email already exists and is not a guest
                    return response()->json(['error' => 'Email is already taken'], 409);
                }
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
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            return response()->json(['error' => $e], 500);
        }
    }

    public function login(Request $request): JsonResponse
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
    public function search($id)
    {
        $user = User::find($id);
        // Check if the user was found
        if (!$user) {
            abort(404, 'User not found');
        }
        return $user;
    }
}
