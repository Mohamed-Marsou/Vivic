<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    protected $fillable = ['url'];
    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }
}
