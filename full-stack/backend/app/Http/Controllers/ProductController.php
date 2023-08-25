<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->get();
    
        if ($products->isEmpty()) {
            return response()->json(['response' => "No Products were found!"]);
        }
    
        return response()->json($products);
    }

    public function newProducts()
    {
        $perPage =4; 
        $products = Product::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Retrieve the maximum price using a subquery
        $maxPrice = DB::table('products')
        ->max('price');
        if ($products->isEmpty()) {
            return response()->json(['response' => "No Products were found!"]);
        }    
        return response()->json([$products , $maxPrice]);
    }
    public function getHighRating()
    {
        $perPage =8; 
        $products = Product::with('images')
            ->orderBy('average_rating', 'desc')
            ->paginate($perPage);

        if ($products->isEmpty()) {
            return response()->json(['response' => "No Products were found!"]);
        } 
        return response()->json($products);
    }
    public function getProduct($slug)
    {
        $product =  Product::with('images')->where('slug', $slug)->first();
    
        if (!$product) {
            return response()->json(['response' => "Product not found!"], 404);
        }
        return response()->json($product);
    }    
    public function getRange($minPrice)
    {
        $products = Product::with('images')
            ->where(function ($query) use ($minPrice) {
                $query->whereRaw("COALESCE(sale_price, price) >= ?", [$minPrice]);
            })
            ->get();
        
        if ($products->isEmpty()) {
            return response()->json(['response' => "No products found within the specified price range"], 404);
        }
    
        return response()->json($products);
    }    

    public function getFilteredProducts(Request $request)
    {
        $query = Product::query();
    
        // Category ID selected in the request
        $categoryId = $request->input('categoryId');
    
        // Filter by Category ID
        $query->where('category_id', $categoryId);
    
        // Checkboxes selected in the request
        $selectedCheckboxes = $request->input('checkboxes', []);
    
        // Check if "sale" checkbox is selected
        if (in_array('sale', $selectedCheckboxes)) {
            $query->where(function ($query) {
                $query->whereNotNull('sale_price')
                      ->orWhere('sale_price', '>', 0);
            });
        }
    
        // Check if "rating" checkbox is selected
        if (in_array('rating', $selectedCheckboxes)) {
            $query->where('average_rating', '>=', 4.0);
        }
    
        // Check if "inStock" checkbox is selected
        if (in_array('inStock', $selectedCheckboxes)) {
            $query->where('inStock', '>', 0);
        }
        $query->with('images');
       // Limit the number of results to 10
        $filteredProducts = $query->take(12)->get();
    
        return response()->json(['data' => $filteredProducts], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validatedData);

        return response()->json($product, 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
