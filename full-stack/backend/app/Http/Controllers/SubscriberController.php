<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        try {
            $subscribers = Subscriber::all();

            if ($subscribers->isEmpty()) {
                return response()->json(['message' => 'No subscribers found'], 404);
            }

            return response()->json(['message' => 'Subscribers retrieved successfully', 'subscribers' => $subscribers], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that might occur
            return response()->json(['message' => 'An error occurred while retrieving subscribers', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);

            $subscriber = Subscriber::create($validatedData);

            return response()->json(['message' => 'Subscriber added successfully', 'subscriber' => $subscriber], 201);
        } catch (\Exception $e) {
            // Handle any exceptions that might occur
            return response()->json(['message' => 'An error occurred while adding the subscriber', 'error' => $e->getMessage()], 500);
        }
    }
}
