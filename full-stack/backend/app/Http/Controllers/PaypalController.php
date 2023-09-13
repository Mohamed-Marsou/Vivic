<?php

namespace App\Http\Controllers;

use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use PayPal\Api\OrderUpdateRequest;
use App\Http\Controllers\Controller;
use PayPal\Auth\OAuthTokenCredential;

class Paypal extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $paypalConfig = config('paypal');

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $paypalConfig['AcR3nv1BrDDyMfFIxX0Tn9DBl564685NwwowwbP4oVfOYrzQ81Fpo5bdtHQyh-ULyTmIEN8Xxsysgg2a'], // TODO change Client ID
                $paypalConfig['EEeMkAQneGpPivpJ3nmtPTVFFMNub2yvSOcqK-jS0EY7TKy07Nl809acwh8IWw2lCBlfYlaC9c8QJMao'] // TODO change Secret key 
            )
        );

        $apiContext->setConfig($paypalConfig['settings']);
    }
    public function updatePaypalTracking(Request $request)
    {
        // Get the transaction ID and tracking number from the request or use hardcoded values for testing
        $transactionId = $request->input('transaction_id', 'YOUR_TRANSACTION_ID');
        $trackingNumber = $request->input('tracking_number', 'YOUR_TRACKING_NUMBER');

        if (!preg_match('/^[A-Za-z0-9]+$/', $trackingNumber)) {
            return response()->json(['error' => 'Invalid tracking number'], 400);
        }

        // Create an order update request
        $orderUpdate = new OrderUpdateRequest();
        
        $orderUpdate->setTransactionId($transactionId);
        $orderUpdate->setOrderStatus('Shipped'); // TODO  ?  Set the order status as needed
        // todo change CarrierName
        $orderUpdate->setTrackingInfo([['carrier' => 'CarrierName', 'tracking_number' => $trackingNumber]]);

        try {
            // Execute the update request
            $orderUpdate->update($this->apiContext);

            // Handle success
            return response()->json(['message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            // Handle error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
