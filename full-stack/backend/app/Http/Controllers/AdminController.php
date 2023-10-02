<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            // Retrieve admins in descending order
            $admins = Admin::orderBy('created_at', 'desc')
                ->paginate(3);
    
            return response()->json(['admins' => $admins], 200);
        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin-api-token')->plainTextToken;

            return response()->json([
                'message' => 'Admin login successful',
                'admin' => $admin->only(['id', 'name']),
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid email or password. Please try again.'], 401);
    }
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:4',
            'adminRole' => 'string|nullable', 
        ]);
    
        // Create a new admin instance
        $admin = new Admin();
        $admin->name = $validatedData['name'];
        $admin->email = $validatedData['email'];
        $admin->password = Hash::make($validatedData['password']);
        $admin->role = $validatedData['adminRole'] ?? 'admin'; // Default role is 'Admin'
        
        // Save the admin to the database
        $admin->save();
    
        // You can return a success response or any other data you need
        return response()->json(['message' => 'Admin created successfully'], 201);
    }
    public function getDashInfo(): JsonResponse
    {
        try {
            // Get the first day of the current month
            $currentMonthStart = Carbon::now()->startOfMonth();

            // Total Income for this month
            $totalIncomeThisMonth = Order::where('created_at', '>=', $currentMonthStart)->sum('amount');

            // Total Sales for this month
            $totalSales = OrderProduct::whereHas('order', function ($query) use ($currentMonthStart) {
                $query->where('created_at', '>=', $currentMonthStart);
            })->sum('quantity');

            // Total Orders for this month
            $totalOrders = Order::where('created_at', '>=', $currentMonthStart)->count();

            // Initialize the data arrays with zeros for the previous 12 months
            $incomeData = array_fill(0, 12, 0);
            $newProductsData = array_fill(0, 6, 0);
            $newUsersData = array_fill(0, 6, 0);

            // Loop through the last 12 months for income data
            for ($i = 0; $i < 12; $i++) {
                // Calculate the start date of the current month in the loop
                $startDate = $currentMonthStart->copy()->subMonths($i);

                // Calculate the end date of the current month in the loop
                $endDate = $startDate->copy()->endOfMonth();

                // Total Income for the current month
                $totalIncome = Order::whereBetween('created_at', [$startDate, $endDate])->sum('amount');

                // Update income data for the current month in reverse order
                $incomeData[11 - $i] = $totalIncome;
            }

            // Loop through the last 6 months for new product and new user data
            for ($i = 0; $i < 6; $i++) {
                // Calculate the start date of the current month in the loop
                $startDate = $currentMonthStart->copy()->subMonths($i);

                // Calculate the end date of the current month in the loop
                $endDate = $startDate->copy()->endOfMonth();

                // Count new Products in the current month
                $newProducts = Product::whereBetween('created_at', [$startDate, $endDate])->count();

                // Update new product data for the current month in reverse order
                $newProductsData[5 - $i] = $newProducts;

                // Count new Users in the current month
                $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

                // Update new user data for the current month in reverse order
                $newUsersData[5 - $i] = $newUsers;
            }

            $data = [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'new_users_this_month' => $newUsersData[5], // New users for the current month
                'income_data' => $incomeData,
                'new_products_data' => $newProductsData,
                'new_users_data' => $newUsersData,
                'total_income_this_month' => $totalIncomeThisMonth,
            ];

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            return response()->json(['error' => 'An error occurred.' . $e], 500);
        }
    }


    public function getRecentOrders(): JsonResponse
    {
        try {
            // Get the last 10 OrderProduct rows from the database
            $recentOrders = OrderProduct::with('order')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
    
            $data = [];
    
            // Process each order product and fetch the product data by product_id
            foreach ($recentOrders as $orderProduct) {
                $product = Product::find($orderProduct->product_id);
    
                if ($product) {
                    // Retrieve all images for the product
                    $productImages = $product->images;
                    
                    // Filter cover image from all images
                    $coverImage = $productImages->where('pivot.is_cover', true)->first();
    
                    // If the product exists, add the relevant information to the data array
                    $data[] = [
                        'product_name' => $product->name,
                        'product_price' => $product->sale_price != '0.00' ? $product->sale_price : $product->price ,
                        'quantity' => $orderProduct->quantity,
                        'cover_image' => $coverImage,
                        'slug' => $product->slug

                    ];
                } else {
                    return response()->json(['error' => 'Something went wrong in Dashboard controller [getRecentOrders] '], 404);
                }
            }
    
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            return response()->json(['error' => 'An error occurred.' . $e], 500);
        }
    }
    public function getOrders(): JsonResponse
    {
        try {
            // Get the last 10 OrderProduct rows from the database
            $recentOrders = OrderProduct::with('order')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
    
            $data = [];
    
            // Process each order product and fetch the product data by product_id
            foreach ($recentOrders as $orderProduct) {
                $product = Product::find($orderProduct->product_id);
    
                if ($product) {
                    // Retrieve all images for the product
                    $productImages = $product->images;
                    
                    // Filter cover image from all images
                    $coverImage = $productImages->where('pivot.is_cover', true)->first();
    
                    // If the product exists, add the relevant information to the data array
                    $data[] = [
                        'product_name' => $product->name,
                        'product_price' => $product->sale_price ?$product->sale_price : $product->price ,
                        'quantity' => $orderProduct->quantity,
                        'cover_image' => $coverImage,
                    ];
                } else {
                    return response()->json(['error' => 'Something went wrong in Dashboard controller [getRecentOrders] '], 404);
                }
            }
    
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            return response()->json(['error' => 'An error occurred.' . $e], 500);
        }
    }
    public function ProductSearch(Request $request): JsonResponse
    {
        $searchQuery = $request->input('q');

        $product = Product::with('category')
            ->where(function($query) use ($searchQuery) {
                $query->whereRaw('lower(name) like ?', ['%' . strtolower($searchQuery) . '%']);
            })
            ->select('id', 'name', 'inStock', 'category_id')
            ->first();
        return response()->json($product);
    }
}
