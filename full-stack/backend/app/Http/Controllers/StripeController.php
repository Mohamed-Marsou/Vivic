<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\User;
use App\Models\Order;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Hash;

class StripeController extends Controller
{
    public function PaymentIntent(Request $request)
    {
        // Retrieve the data from the request
        $paymentMethodId = $request->input('payment_method_id');
        $amount = $request->input('amount');
        $userId = $request->input('userId');
        $wpOrderID = $request->input('wp_order_id');


        $userName = $request->input('costumerName');
        $userEmail = $request->input('costumerEmail');
        $userAddress = $request->input('costumerAddress');
        $userCity = $request->input('costumerCity');
        $userCountry = $request->input('costumerCountry');

        // Convert the amount to cents
        $amountInCents = intval($amount * 100);
        // Set your Stripe API secret key
       $stripeSecretKey = env('STRIPE_SECRET');
       Stripe::setApiKey($stripeSecretKey);

        // Create a new payment intent
        try {
            PaymentIntent::create([
                'amount' => $amountInCents, // The amount in cents
                'currency' => 'usd',
                'payment_method' => $paymentMethodId,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => 'http://localhost',
            ]);

            // Create a new user if userId is null
            if ($userId === null) {
                // Check if a user with this email already exists
                $existingUser = User::where('email', $userEmail)->first();

                if ($existingUser) {
                    // User with the same email exists, assign the existing user's ID
                    $userId = $existingUser->id;
                } else {
                    // Create a new user
                    $user = new User();
                    $user->name = $userName;
                    $user->email = $userEmail;
                    $user->address = $userAddress;
                    $user->city = $userCity;
                    $user->country = $userCountry;
                    $user->password = Hash::make('password');
                    $user->save();
                    // Assign the new user's ID to $userId
                    $userId = $user->id;
                }
            }
            //Create a new order
            $order = new Order();
            $order->user_id = $userId;
            $order->amount = $amount;
            $order->transaction_id = $paymentMethodId;
            $order->wp_order_id = $wpOrderID;
            // Save the order
            $order->save();

            // Handle the success response
            return response()->json([
                'success' => true,
                'order_id'=> $order->id
            ], 200);
        } catch (\Exception $e) {
            // Handle the error response
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
