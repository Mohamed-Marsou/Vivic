<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function index(): JsonResponse
    {
        
        $orders = Order::with('products')
        ->orderByDesc('created_at')
        ->paginate(10);

        if (!$orders) {
            return response()->json(['error' => 'Orders not found'], 404);
        }
        return response()->json($orders, 200);
    }
    public function getOrderProducts($order_id): JsonResponse
    {
        $order = Order::with('products')->where('transaction_id', $order_id)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order, 200);
    }

    public function addOrderProducts(Request $request): JsonResponse
    {
        // Retrieve the order ID and products data from the request
        $orderId = $request->input('order_id');
        $products = $request->input('products');
    
        // Loop through the products and process each one
        foreach ($products as $productData) {
            $quantity = $productData['quantity'];
            $productId = $productData['product_id'];
    
            // Create a new order_product record
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $orderId;
            $orderProduct->product_id = $productId;
            $orderProduct->quantity = $quantity;
            $orderProduct->save();
    
            // Decrease the quantity of the product in the product table
            $product = Product::find($productId);
            $product->inStock -= $quantity;
            $product->save();
        }
    
        // Return a response indicating success
        return response()->json([
            'success' => true,
            'message' => 'Products added to the order successfully.',
        ],201);
    }


}
