<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use PayPal\Http\Request as PayPalRequest;
use PayPal\Http\Response as PayPalResponse;

class MyPaypalController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $paypal_conf = \Config::get('paypal');
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret']
            )
        );
        $this->apiContext->setConfig($paypal_conf['settings']);
    }

    public function updatePaypalTracking(Request $request)
    {
        // Get the order ID and tracking number from the request or use hardcoded values for testing
        $orderId = $request->input('order_id', 'YOUR_ORDER_ID');
        $trackingNumber = $request->input('tracking_number', 'YOUR_TRACKING_NUMBER');

        if (!preg_match('/^[A-Za-z0-9]+$/', $trackingNumber)) {
            return response()->json(['error' => 'Invalid tracking number'], 400);
        }

        // Create a PayPal HTTP request to update the order with tracking information
        $paypalRequest = new PayPalRequest();
        $paypalRequest->method = 'PATCH';
        $paypalRequest->headers['Content-Type'] = 'application/json';
        $paypalRequest->headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        $paypalRequest->path = "/v2/checkout/orders/{$orderId}";
        $paypalRequest->body = [
            'tracking_info' => [
                [
                    'carrier' => 'CarrierName', // Replace with actual carrier name
                    'tracking_number' => $trackingNumber,
                ]
            ]
        ];

        // Execute the request
        $response = $this->apiContext->execute($paypalRequest);

        if ($response instanceof PayPalResponse) {
            // Handle success
            return response()->json(['message' => 'Order updated successfully']);
        } else {
            // Handle error
            return response()->json(['error' => 'Failed to update order'], 500);
        }
    }
 
}