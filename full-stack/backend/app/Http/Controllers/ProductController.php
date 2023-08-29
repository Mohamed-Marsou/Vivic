<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Cart;
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
        $perPage = 4;
        $products = Product::with('images')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Retrieve the maximum price using a subquery
        $maxPrice = DB::table('products')
            ->max('price');
        if ($products->isEmpty()) {
            return response()->json(['response' => "No Products were found!"]);
        }
        return response()->json([$products, $maxPrice]);
    }
    public function getHighRating()
    {
        $perPage = 8;
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

    public function addToWishlist(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'productId' => 'required',
                'userId' => 'required',
            ]);

            // Check if the item is already in the wishlist
            $existingWishlistItem = Wishlist::where('product_id', $validatedData['productId'])
                ->where('user_id', $validatedData['userId'])
                ->first();

            if ($existingWishlistItem) {
                return response()->json(['message' => 'Item is already in the wishlist'], 200);
            }

            // Create a new wishlist record
            $wishlist = new Wishlist();
            $wishlist->product_id = $validatedData['productId'];
            $wishlist->user_id = $validatedData['userId'];
            $wishlist->save();

            return response()->json(['message' => 'Item added to wishlist'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add item to wishlist', 'error' => $e->getMessage()], 500);
        }
    }
    public function addToCart(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'productId' => 'required',
                'userId' => 'required',
            ]);

            // Check if the item is already in the Cart
            $existinginCartItem = Cart::where('product_id', $validatedData['productId'])
                ->where('user_id', $validatedData['userId'])
                ->first();

            if ($existinginCartItem) {
                // Increase the quantity by 1
                $existinginCartItem->increment('quantity');
                return response()->json(['message' => 'Quantity increased in the Cart'], 200);
            }

            // Create a new Cart record
            $cart = new Cart();
            $cart->product_id = $validatedData['productId'];
            $cart->user_id = $validatedData['userId'];
            $cart->save();

            return response()->json(['message' => 'Item added to Cart'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add item to Cart', 'error' => $e->getMessage()], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $productIds = $request->input('productIds'); 
            
            // Fetch products based on the array of product IDs
            $products = Product::whereIn('id', $productIds)->with('images')->get();

            if ($products->isEmpty()) {
                return response()->json(['response' => "No products found."], 200);
            }
            $count = count($productIds); 
            return response()->json(['products' => $products, 'count'=> $count], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving products', 'error' => $e->getMessage()], 500);
        }
    }


    public function getUserWishListProducts($id)
    {
        $wishlistItems = Wishlist::where('user_id', $id)
            ->with('product.images')
            ->get();
        
        if ($wishlistItems->isEmpty()) {
            return response()->json(['response' => "No products in wishlist."], 200);
        }
    
        $productsWithImages = $wishlistItems->map(function ($wishlistItem) {
            $product = $wishlistItem->product;
            $images = $product->images;
            $product->images = $images;
            return $product;
        });
    
        $wishlistCount = count($wishlistItems);
    
        return response()->json([
            'wishlistCount' => $wishlistCount,
            'products' => $productsWithImages
        ], 200);
    }

    public function getInCartProducts($id)
    {
        $inCartItems = Cart::where('user_id', $id)
            ->with('product.images')
            ->get();
        
        if ($inCartItems->isEmpty()) {
            return response()->json(['response' => "No products inCart."], 200);
        }
    
        $productIds = $inCartItems->pluck('product_id');
    
        $productsWithImages = Product::whereIn('id', $productIds)
            ->with('images')
            ->get();
    
        // Combine cart items with corresponding products and images
        $combinedItems = $inCartItems->map(function ($inCartItem) use ($productsWithImages) {
            $product = $productsWithImages->firstWhere('id', $inCartItem->product_id);
            return [
                'product' => $product,
                'quantity' => $inCartItem->quantity 
            ];
        });
    
        $inCartlistCount = count($inCartItems);
    
        return response()->json([
            'inCartlistCount' => $inCartlistCount,
            'products' => $combinedItems
        ], 200);
    }

    public function removeInCartProducts($userId, $productId)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json(['message' => 'Product removed from cart'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing product from cart'], 500);
        }
    }
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
