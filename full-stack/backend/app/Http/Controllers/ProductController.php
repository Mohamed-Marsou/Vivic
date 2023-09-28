<?php

namespace App\Http\Controllers;

use DOMDocument;
use App\Models\Cart;
use App\Models\User;
use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    private $baseUrl;
    private $endpoint;
    private $WOO_CK;
    private $WOO_CS;

    public function __construct()
    {
        $this->baseUrl = env('WOO_URL');
        $this->endpoint = '/products';
        $this->WOO_CK = env('WOOCOMMERCE_API_KEY');
        $this->WOO_CS = env('WOOCOMMERCE_API_SECRET');
    }
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
        $perPage = 5;
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
        $perPage = 5;
        $products = Product::with('images')
            ->orderBy('average_rating', 'desc')
            ->paginate($perPage);

        if ($products->isEmpty()) {
            return response()->json(['response' => "No Products were found!"]);
        }
        return response()->json($products);
    }
    public function getFeatured()
    {
        try {
            $onSaleProducts = Product::where('on_sale', true)->with('images')->take(10)->get();

            // Count the number of products on sale
            $countOnSale = $onSaleProducts->count();

            if ($countOnSale < 10) {
                $remainingProductsCount = 10 - $countOnSale;
                // Query the database to get unique regular products that are not already included
                $regularProducts = Product::where('on_sale', false)
                    ->whereNotIn('id', $onSaleProducts->pluck('id'))
                    ->with('images')
                    ->take($remainingProductsCount)
                    ->get();

                // Combine on-sale and unique regular products
                $featuredProducts = $onSaleProducts->concat($regularProducts);
            } else {
                $featuredProducts = $onSaleProducts;
            }
            return response()->json(['data' => $featuredProducts]);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the process
            return response()->json(['error' => 'An error occurred while fetching featured products' . $e], 500);
        }
    }

    public function getProduct($slug)
    {
        $baseProduct = Product::with('images')->where('slug', $slug)->first();
    
        if (!$baseProduct) {
            return response()->json(['response' => "Product not found!"], 404);
        }
    
        $productVariants = ProductVariant::where('product_id', $baseProduct->id)->get();
    
        $productData = [
            'baseProduct' => $baseProduct,
        ];
        if (!$productVariants->isEmpty()) {
            $productData['variants'] = $productVariants;
        }
    
        return response()->json($productData);
    }
    
    public function getRange($minPrice)
    {
        $products = Product::with('images')
            ->where(function ($query) use ($minPrice) {
                $query->where(function ($subquery) use ($minPrice) {
                    $subquery->where('sale_price', '!=', '0.00')
                        ->where('sale_price', '>=', $minPrice);
                })->orWhere(function ($subquery) use ($minPrice) {
                    $subquery->where('price', '>=', $minPrice);
                });
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
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

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
    public function addToWishlist(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'userId' => 'required',
                'Id' => 'required',
                'SKU' => 'required',
            ]);

            // Check if the item is already in the wishlist
            $existingWishlistItem = Wishlist::where('product_id', $validatedData['Id'])
                ->where('user_id', $validatedData['userId'])
                ->where('SKU', $validatedData['SKU'])
                ->first();

            if ($existingWishlistItem) {
                return response()->json(['message' => 'Item is already in the wishlist'], 200);
            }

            // Create a new wishlist record
            $wishlist = new Wishlist();
            $wishlist->user_id = $validatedData['userId'];
            $wishlist->product_id = $validatedData['Id'];
            $wishlist->SKU = $validatedData['SKU'];
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
                'userId' => 'required',
                'productId' => 'required',
                'SKU' => 'required',
            ]);

            // Check if the item is already in the Cart
            $existingCartItem = Cart::where('product_id', $validatedData['productId'])
            ->where('user_id', $validatedData['userId'])
            ->where('SKU', $validatedData['SKU'])
            ->first();

            if ($existingCartItem) {
                // Increase the quantity by 1
                $existingCartItem->increment('quantity');
                return response()->json(['message' => 'Quantity increased in the Cart'], 200);
            }

            // Create a new Cart record
            $cart = new Cart();
            $cart->user_id = $validatedData['userId'];
            $cart->product_id = $validatedData['productId'];
            $cart->SKU = $validatedData['SKU'];
            $cart->save();
            return response()->json(['message' => 'Item added to Cart'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add item to Cart', 'error' => $e->getMessage()], 500);
        }
    }
    public function getProducts(Request $request)
    {
        try {
            $requestProducts = $request->input('products');
    
            $result = [];
    
            foreach ($requestProducts as $productInfo) {
                $productId = $productInfo['productId'];
                $SKU = $productInfo['SKU'];
    
                // Attempt to fetch the product by productId
                $product = Product::with('images')->where('id', $productId)->first();
    
                if ($product && $product->SKU === $SKU) {
                    $result[] = $product;
                } else {
                    // If no product by productId is found or SKU doesn't match, search in 'products_variant'
                    $variant = ProductVariant::where('product_id', $productId)
                        ->where('SKU', $SKU)
                        ->first();
                        
                    if ($variant) {
                        $variant->id = $variant->product_id;
                        unset($variant->product_id);

                        $result[] = $variant;
                    }
                }
            }
    
            if (empty($result)) {
                return response()->json(['response' => "No products found."], 200);
            }
    
            $count = count($result);
            return response()->json(['products' => $result, 'count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving products', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getUserWishListProducts($id)
    {
        $wishlistItems = Wishlist::where('user_id', $id)->get();
    
        if ($wishlistItems->isEmpty()) {
            return response()->json(['response' => "No products in wishlist."], 200);
        }
    
        $products = $wishlistItems->map(function ($wishlistItem) {
            // Check if the product exists in the Product table by ID
            $product = Product::where('id', $wishlistItem->product_id)
            ->where('SKU',$wishlistItem->SKU)
            ->with('images')->first();
    
            // If the product doesn't exist by ID, check by SKU in the Product table
            if (!$product) {
                $product = ProductVariant::where('SKU', $wishlistItem->SKU)->first();
                $product->id = $product->product_id;
                unset($product->product_id);
            }
            return $product;
        });
    
        $wishlistCount = count($wishlistItems);
    
        return response()->json([
            'wishlistCount' => $wishlistCount,
            'products' => $products
        ], 200);
    }
    
    public function getInCartProducts($id)
    {
        $inCartItems = Cart::where('user_id', $id)->get();
    
        if ($inCartItems->isEmpty()) {
            return response()->json(['response' => "No products inCart."], 200);
        }
    
        $combinedItems = $inCartItems->map(function ($inCartItem) {
            // Check if the product exists in the Product table by ID
            $product = Product::where('id', $inCartItem->product_id)
            ->where('SKU',$inCartItem->SKU)
            ->with('images')->first();
    
            // 
            if (!$product) {
                $product = ProductVariant::where('SKU', $inCartItem->SKU)->first();
                $product->id = $product->product_id;
                unset($product->product_id);
            }
    
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
    public function decreaseCartProductQuantity($userId, $productId, $SKU)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->orWhere('SKU', $SKU)
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
    public function increaseCartProductQuantity($userId, $productId,$SKU)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->orWhere('SKU', $SKU)
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
    public function removeWishlistProducts($userId, $SKU)
    {
        try {
            $cartItem = Wishlist::where('user_id', $userId)
                ->where('SKU', $SKU)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json(['message' => 'Product removed from Wishlist'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found in Wishlist'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing product from Wishlist'], 500);
        }
    }
    public function removeInCartProducts($userId, $productId ,$SKU)
    {
        try {
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->orWhere('SKU', $SKU)
                ->firstOrFail();

            $cartItem->delete();

            return response()->json(['message' => 'Product removed from cart'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found in cart ' . $e ], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing product from cart ' . $e], 404);
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
    public function destroy($id)
    {
        // Find the product by its ID
        $product = Product::find($id);

        // Check if the product exists
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Retrieve the category associated with the product
        $category = $product->category;

        // Delete the product
        $product->delete();

        // * Delete product-related images
        $this->deleteDir('product-images', $product['slug']);
        $this->deleteDir('description-images', $product['slug']);
        $this->deleteDir('category-images', $category->name);
        
        //! Check if the category  has no products
        if ($category->products->isEmpty()) {
            // Delete the category
            $category->delete();
        }

        return response()->json(['response' => 'Product was deleted !!'], 201);
    }

    //* ********************************* import Woo Products to DATABASE 

    public function syncAllProductsFromWooCommerce(): JsonResponse
    {
        $perPage = 50;
        $currentPage = 1;
        $successCount = 0;

        do {
            try {
                $response = Http::withBasicAuth($this->WOO_CK, $this->WOO_CS)
                    ->get($this->baseUrl . $this->endpoint, [
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

                        $categories = $productData['categories'][0]; //? CHANGE LETER to handle multi categories

                        $categoryId = $this->saveCategories($categories);
                        // Save the new product
                        $newProduct = $this->saveProduct($productData, $categoryId);
                        // Increment the successful count
                        $successCount++;
                        $isFirstImage = true;

                        if ($newProduct->id) {
                            // Check if the folder exists 
                            $this->deleteDir('product-images', $productData['slug']);
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
                        // * - save Products Variation 
                        if ($newProduct) {
                            $this->handelVariant($newProduct['name'], $productData['id'] ,$newProduct['id'], $newProduct['slug']);
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
    public function syncProductsFromWooCommerce(Request $request): JsonResponse
    {
        $perPage = 35;
        $currentPage = 1;
        $successCount = 0;
        do {
            try {
                $response = Http::withBasicAuth($this->WOO_CK, $this->WOO_CS)
                    ->get($this->baseUrl . $this->endpoint, [
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
                            $categories = $productData['categories'][0]; // CHANGE LATER to handle multi categories if needed

                            $categoryId = $this->saveCategories($categories);
                            // Save the new product
                            $newProduct = $this->saveProduct($productData, $categoryId);

                            $successCount++;
                            $isFirstImage = true;

                            if ($newProduct->id) {

                                // Check if the folder exists 
                                $this->deleteDir('product-images', $productData['slug']);

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
                            // * - save Products Variation 
                            if ($newProduct) {
                                $this->handelVariant($newProduct['name'], $productData['id'],$newProduct['id'], $newProduct['slug']);
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
            'successCount' => $successCount,
        ]);
    }

    // *********************************
    //? checks if image dir already exist and deleting it 
    private function deleteDir($folderName, $slug)
    {
        $folderPath = 'public/' . $folderName . '/' . $slug;
        if (Storage::exists($folderPath)) {
            Storage::deleteDirectory($folderPath);
        }
    }
    private function saveCategories($category)
    {
        $id = [];
        $existCategory = Category::where('name', $category['name'])->first();

        if ($existCategory) {
            $id = $existCategory['id'];
        } else {

            $urlEndPoint = '/products/categories/' . $category['id'];

            try {
                $response = Http::withBasicAuth($this->WOO_CK, $this->WOO_CS)
                    ->get($this->baseUrl . $urlEndPoint);

                $categoryDATA = $response->json();

                // ? Handle image download and storage
                $imgPath = $this->downloadCategoryImages($categoryDATA['image']['src'], $categoryDATA['slug']);

                // save category 
                $newCategory = new Category([
                    'name' => $categoryDATA['name'],
                    'description' => $categoryDATA['description'],
                    'image' =>  $imgPath
                ]);

                $newCategory->save();
                $id = $newCategory['id'];
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return $id;
    }

    private function downloadCategoryImages($src, $slug)
    {
        $imageName = basename($src);
        // Check if the folder exists and the image file exists within it
        $this->deleteDir('category-images', $slug);
        // Create a context with SSL certificate verification disabled
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $imageData = file_get_contents($src, false, $context);
        $imagePath = '/storage/category-images/' . $slug . '/' . $imageName;
        Storage::put('public/category-images/' . $slug . '/' . $imageName, $imageData);
        $imageUrl = url('/') . $imagePath;

        return $imageUrl;
    }
    private function DownloadProductImages($src, $slug): array
    {
        $imageName = basename($src);
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
            if (($startDate && $createdAt >= strtotime($startDate)) || ($finishDate && $createdAt <= strtotime($finishDate))) {
                return false;
            }
        }

        $productIds = $request->input('productIds');
        if ($productIds && !in_array($product['id'], explode(',', $productIds))) {
            return false;
        }
        return true;
    }
    private function saveProduct($productData,  $categoryId): Product
    {
        // Pass the description to save Description Images and get the modified description with correct URLs
        $descriptionWithUpdatedImages = $this->saveDescriptionImages($productData['description'], $productData['slug']);

        $newProduct = new Product([
            'name' => $productData['name'],
            'price' => (float)$productData['price'],
            'regular_price' => (float)$productData['regular_price'],
            'sale_price' => (float)$productData['sale_price'],
            'average_rating' => (float)$productData['average_rating'],
            'inStock' => $productData['stock_quantity'],
            'description' =>  $descriptionWithUpdatedImages,
            'category_id' =>  $categoryId,
            'slug' => $productData['slug'],
            'status' => $productData['status'],
            'short_description' => $productData['short_description'],
            'weight' => $productData['weight'],
            'dimensions' => json_encode($productData['dimensions']),
            'specification' => json_encode($productData['attributes']),

            'on_sale' => $productData['on_sale'],
            'date_on_sale_from' => $productData['date_on_sale_from'],
            'date_on_sale_to' =>  $productData['date_on_sale_to'],

            'SKU' => $productData['sku'],

        ]);

        // Save the new product
        $newProduct->save();
        return  $newProduct;
    }
    private function saveDescriptionImages($description, $slug)
    {
        // Create a new DOMDocument
        $dom = new DOMDocument();
        // Suppress HTML5 validation warnings
        libxml_use_internal_errors(true);
        $dom->loadHTML($description);
        libxml_clear_errors();

        // Find all <img> elements
        $imgElements = $dom->getElementsByTagName('img');

        // Initialize an array to store image URLs
        $imageUrls = [];

        // Check if the folder exists and the image file exists within it
        $this->deleteDir('description-images', $slug);
        // Loop through each <img> element and process the image URL
        foreach ($imgElements as $imgElement) {
            $imageUrl = $imgElement->getAttribute('src');

            // Download the image and save it to the file system
            $newImagePath = $this->downloadAndSaveImage($imageUrl, $slug);

            // Replace the old URL with the new file path in the HTML
            $imgElement->setAttribute('src', $newImagePath);

            // Store the new image URL
            $imageUrls[] = $newImagePath;
        }

        // Get the modified HTML content
        $modifiedDescription = $dom->saveHTML();

        return $modifiedDescription;
    }
    private function downloadAndSaveImage($src, $slug)
    {
        $this->deleteDir('description-images', $slug);
        $imageName = basename($src);
        // Create a context with SSL certificate verification disabled
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $imageData = file_get_contents($src, false, $context);
        $imagePath = '/storage/description-images/' . $slug . '/' . $imageName;
        Storage::put('public/description-images/' . $slug . '/' . $imageName, $imageData);
        $imageUrl = url('/') . $imagePath;

        return $imageUrl;
    }
    private function handelVariant($pName, $woPId,$productId, $slug)
    {
        $endpoint = '/products/' . $woPId . '/variations';
        try {
            $response = Http::withBasicAuth($this->WOO_CK, $this->WOO_CS)
                ->get($this->baseUrl . $endpoint);

            $variants = $response->json();
            $this->deleteDir('variant-images', $slug);
            // save each variant 
            foreach ($variants as $variant) {
                // Check if the folder exists and the image file exists within it
                $newVariantImagePath = $this->downloadVariantImg($variant['image']['src'], $slug);

                // Check if image download was successful
                if ($newVariantImagePath) {
                    // Create and save the ProductVariant
                    ProductVariant::create([
                        'product_id' => $productId,
                        // adding product variant name 
                        'name' => $pName,
                        'SKU' => $variant['sku'],
                        'price' => $variant['price'],
                        'attributes' => json_encode($variant['attributes']),
                        'regular_price' => $variant['regular_price'],
                        'sale_price' => $variant['sale_price'],
                        'inStock' => $variant['stock_quantity'],
                        'weight' => $variant['weight'],
                        'dimensions' => json_encode($variant['dimensions']),
                        'image' => $newVariantImagePath,
                    ]);
                } else {
                    // Handle the case where image download failed
                    Log::error('Image download failed for variant: ' . json_encode($variants));
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error at handelVariant() :  ' => $e->getMessage()], 500);
        }
    }
    private function downloadVariantImg($src, $slug)
    {
        $imageName = basename($src);

        // Create a context with SSL certificate verification disabled
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $imageData = file_get_contents($src, false, $context);
        $imagePath = '/storage/variant-images/' . $slug . '/' . $imageName;
        Storage::put('public/variant-images/' . $slug . '/' . $imageName, $imageData);
        $imageUrl = url('/') . $imagePath;

        return $imageUrl;
    }
}
