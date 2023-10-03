<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'average_rating',
        'title',
        'author',
        'email',
        'body_text',
        'body_url',
        'country_code',
        'status',
        'feature',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'slug', 'slug');
    }
}
