<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Image;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'price','sale_price','regular_price', 'slug','average_rating', 'short_description', 'specification', 'status',
        'weight', 'dimensions', 'inStock', 'description','category_id','on_sale','date_on_sale_from','date_on_sale_to'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot('quantity');
    }
    public function images()
    {
        return $this->belongsToMany(Image::class, 'product_image')
            ->withPivot('is_cover');
    }
    
}
