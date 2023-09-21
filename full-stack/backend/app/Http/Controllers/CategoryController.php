<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
        ->orderBy('created_at', 'desc') 
        ->take(7)
        ->get();
    
        if($categories->isEmpty()){
            return response()->json(['response' => 'No Categories were Found !!!']);
        }
    
        return response()->json(['response' =>  $categories]);
    }
    
    
    public function getCategoryProducts($id)
    {   
        $categoryData = Category::find($id);
        $category = Category::with('products')->find($id);
        
        if ($category) {
            $products = $category->products()->with('images')->paginate(8);
    
            // Retrieve the maximum price using a subquery
            $maxPrice = DB::table('products')
                ->where('category_id', $id)
                ->max('price');
    
            return response()->json(['products' => $products, 'category' => $categoryData, 'maxPrice' => $maxPrice], 200);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
    
}
