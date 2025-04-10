<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'description',
        'price',
        'rating',
        'brand',
        'category',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'float',
        'rating' => 'float',
    ];
}
