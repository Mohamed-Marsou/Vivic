<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Image;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function index()
    {
        $perPage = 8;
        $products = Product::with(['images', 'category:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

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
            return response()->json(['products' => $products, 'count' => $count], 200);
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
    public function decreaseCartProductQuantity($userId, $productId)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->firstOrFail();

            // Decrease the quantity by 1 if it's greater than 1
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            }

            return response()->json(['message' => 'Cart product quantity updated successfully.', 'product' => $cartItem], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating cart product quantity. ' . $e], 500);
        }
    }
    public function increaseCartProductQuantity($userId, $productId)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->firstOrFail();

            $product = $cartItem->product;

            // Increase the quantity by 1 if it's less than the product's inStock value
            if ($cartItem->quantity < $product->inStock) {
                $cartItem->increment('quantity');
            }
            return response()->json(['message' => 'Cart product quantity updated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating cart product quantity.'], 500);
        }
    }
    public function removeWishlistProducts($userId, $productId)
    {
        try {
            $cartItem = Wishlist::where('user_id', $userId)
                ->where('product_id', $productId)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json(['message' => 'Product removed from Wishlist'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found in Wishlist'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing product from Wishlist'], 500);
        }
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
    public function clearUserCart($id)
    {
        try {
            // Find the user by their ID
            $user = User::findOrFail($id);

            // Delete the user's cart if it exists
            if ($user->cart) {
                $user->cart->delete();
            }

            return response()->json(['message' => 'User cart cleared successfully'], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur (e.g., user not found)
            return response()->json(['message' => 'Failed to clear user cart: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }

    //* ********************************* import Woo Products to DATABASE 

    public function syncAllProductsFromWooCommerce(): JsonResponse
    {
        $baseUrl = env('WOO_URL');
        $endpoint = '/products';

        $WOO_CK = env('WOOCOMMERCE_API_KEY');
        $WOO_CS = env('WOOCOMMERCE_API_SECRET');

        $perPage = 50;
        $currentPage = 1;
        $successCount = 0;
        do {
            try {
                $response = Http::withBasicAuth($WOO_CK, $WOO_CS)
                    ->get($baseUrl . $endpoint, [
                        'per_page' => $perPage,
                        'page' => $currentPage,
                    ]);

                $products = $response->json();
                foreach ($products as $productData) {
                    // Check if the product already exists in the database by slug
                    $existingProduct = Product::where('slug', $productData['slug'])
                        ->first();
                    // Check if the status in $productData is 'publish'
                    if (!$existingProduct && $productData['status'] === 'publish') {
                        // Create a new Product instance and fill it with data
                        $newProduct = new Product([
                            'name' => $productData['name'],
                            'price' => (float)$productData['price'],
                            'regular_price' => (float)$productData['regular_price'],
                            'sale_price' => (float)$productData['sale_price'],
                            'average_rating' => (float)$productData['average_rating'],
                            'inStock' => $productData['stock_quantity'],
                            'description' => $productData['description'],
                            'category_id' => rand(1, 6),
                            'slug' => $productData['slug'],
                            'status' => $productData['status'],
                            'short_description' => $productData['short_description'],
                            'weight' => $productData['weight'],
                            'dimensions' => json_encode($productData['dimensions']),
                            'specification' => json_encode($productData['attributes']),
                        ]);

                        // Save the new product
                        $newProduct->save();
                        // Increment the successful count
                        $successCount++;
                        $isFirstImage = true;

                        if ($newProduct->id) {

                            foreach ($productData['images'] as $image) {
                                $imageInfo = $this->DownloadProductImages(
                                    $image['src'],
                                    $productData['slug']
                                );

                                // Add the image ID to the product_images pivot table
                                $newProduct->images()->attach($imageInfo['id'], ['product_id' => $newProduct->id]);

                                if ($isFirstImage) {
                                    $newProduct->images()->updateExistingPivot($imageInfo['id'], ['is_cover' => true]);
                                    $isFirstImage = false;
                                }
                            }
                        }
                    }
                }
                $currentPage++;
            } catch (\Exception $e) {
                // Handle any errors that occurred during the API request
                Log::error('syncAllProductsFromWooCommerce API Request Error: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } while (!empty($products));

        // Synchronization completed, return success message
        return response()->json([
            'message' => 'Products retrieved and processed successfully.',
            'successCount' => $successCount, 
        ]);
    }

    // *********************************
    // ********************************* *********************************
    // ********************************* ********************************* *********************************
    public function syncProductsFromWooCommerce(Request $request): JsonResponse
    {
        $baseUrl = env('WOO_URL');
        $endpoint = '/products';

        $WOO_CK = env('WOOCOMMERCE_API_KEY');
        $WOO_CS = env('WOOCOMMERCE_API_SECRET');

        $perPage = 25;
        $currentPage = 1;
        $successCount = 0;
        do {
            try {
                $response = Http::withBasicAuth($WOO_CK, $WOO_CS)
                    ->get($baseUrl . $endpoint, [
                        'per_page' => $perPage,
                        'page' => $currentPage,
                    ]);

                $products = $response->json();

                if (empty($products)) {
                    break;
                }
                // Apply filtering to each fetched product
                foreach ($products as $productData) {
                    if ($this->passesFilters($productData, $request)) {
                        // Check if a product with the same slug already exists
                        $existingProduct = Product::where('slug', $productData['slug'])->first();
                        if (!$existingProduct) {
                            // Create a new Product instance and fill it with data
                            $newProduct = new Product([
                                'name' => $productData['name'],
                                'price' => (float)$productData['price'],
                                'regular_price' => (float)$productData['regular_price'],
                                'sale_price' => (float)$productData['sale_price'],
                                'average_rating' => (float)$productData['average_rating'],
                                'inStock' => $productData['stock_quantity'],
                                'description' => $productData['description'],
                                'category_id' => rand(1, 6),
                                'slug' => $productData['slug'],
                                'status' => $productData['status'],
                                'short_description' => $productData['short_description'],
                                'weight' => $productData['weight'],
                                'dimensions' => json_encode($productData['dimensions']),
                                'specification' => json_encode($productData['attributes']),
                            ]);
    
                            // Save the new product
                            $newProduct->save();
                            $successCount ++;
                            $isFirstImage = true;
    
                            if ($newProduct->id) {
    
                                foreach ($productData['images'] as $image) {
                                    $imageInfo = $this->DownloadProductImages(
                                        $image['src'],
                                        $productData['slug']
                                    );
    
                                    // Add the image ID to the product_images pivot table
                                    $newProduct->images()->attach($imageInfo['id'], ['product_id' => $newProduct->id]);
    
                                    if ($isFirstImage) {
                                        $newProduct->images()->updateExistingPivot($imageInfo['id'], ['is_cover' => true]);
                                        $isFirstImage = false;
                                    }
                                }
                            }
                        }
                    }
                }
                $currentPage++;

            } catch (\Exception $e) {
                // Handle any errors that occurred during the API request
                Log::error('API Request Error [syncProductsFromWooCommerce]: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }

        } while (!empty($products));

        
        return response()->json([
            'message' => 'Products retrieved and processed successfully.',
             'successCount' => $successCount ,
        ]);
    }

    public function DownloadProductImages($src, $slug): array
    {
        $folderPath = 'public/product-images/' . $slug;
        $imageName = basename($src);

        // Check if the folder exists and the image file exists within it
        if (Storage::exists($folderPath) && Storage::exists($folderPath . '/' . $imageName)) {
            Storage::delete($folderPath . '/' . $imageName);
        }

        // Create a context with SSL certificate verification disabled
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $imageData = file_get_contents($src, false, $context);
        $imagePath = '/storage/product-images/' . $slug . '/' . $imageName;
        Storage::put('public/product-images/' . $slug . '/' . $imageName, $imageData);
        $imageUrl = url('/') . $imagePath;

        // Create a new Image instance and save it
        $newImage = new Image(['url' => $imageUrl]);
        $newImage->save();

        // Return the image ID and URL
        return [
            'id' => $newImage->id,
            'url' => $imageUrl,
        ];
    }

    private function passesFilters($product, $request): bool
    {
        $status = $request->input('status');
        if ($status !== 'all') {
            if ($product['status'] !== $status) {
                return false;
            }
        }

        $startDate = $request->input('startDate');
        $finishDate = $request->input('finishDate');

        if (($startDate || $finishDate) && isset($product['date_created'])) {
            $createdAt = strtotime($product['date_created']);
            if (($startDate && $createdAt <= strtotime($startDate)) || ($finishDate && $createdAt >= strtotime($finishDate))) {
                return false;
            }
        }

        $productIds = $request->input('productIds');
        if ($productIds && !in_array($product['id'], explode(',', $productIds))) {
            return false;
        }
        return true;
    }

}
