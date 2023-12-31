<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
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
    public function getUserOrders($id): JsonResponse
    {
        // Retrieve the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Retrieve the user's orders, paginated, with products and cover images
        $orders = $user->orders()
            ->with(['products.images' => function ($query) {
                $query->wherePivot('is_cover', true);
            }])->orderBy('created_at', 'desc')
            ->paginate(7);

        return response()->json(['orders' => $orders]);
    }
    public function getOrderProducts($order_id): JsonResponse
    {
        $order = Order::with(['orderProducts'])->where('wp_order_id', $order_id)->first();
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Initialize an array to store the product data
        $products = [];
    
        // Loop through the order products and add product data
        foreach ($order->orderProducts as $orderProduct) {
            // Try to find the product by SKU in the Product table
            $product = Product::where('SKU', $orderProduct->SKU)->first();
    
            if (!$product) {
                // If not found in Product table, try to find in ProductVariant table
                $product = ProductVariant::where('SKU', $orderProduct->SKU)->first();
            }
    
            // Add the product to the array
            if ($product) {
                $products[] = $product;
            }
        }
    
        // Add the products array to the order object
        $order->products = $products;
    
        return response()->json($order, 200);
    }

    public function addOrderProducts(Request $request): JsonResponse
    {
        // Retrieve the order ID and products data from the request
        $orderId = $request->input('order_id');
        $products = $request->input('products');

        // Loop through the products and process each one
        foreach ($products as $productData) {
            $productId = $productData['product_id'];
            $quantity = $productData['quantity'];
            $SKU = $productData['SKU'];

            // Create a new order_product record
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $orderId;

            $orderProduct->product_id = $productId;
            $orderProduct->quantity = $quantity;
            $orderProduct->SKU = $SKU;
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
        ], 201);
    }
}
