<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;
    protected $table = 'products_variant';
    protected $fillable = [
        'name',
        'price',
        'SKU',
        'regular_price',
        'sale_price',
        'inStock',
        'weight',
        'dimensions',
        'image',
        'product_id',
        'attributes'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
