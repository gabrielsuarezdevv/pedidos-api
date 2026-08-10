<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    // The $fillable property specifies which attributes can be mass-assigned. This is important for security to prevent mass assignment vulnerabilities. In this case, the attributes that can be mass-assigned are 'product_category_id', 'name', 'sku', 'description', 'price', 'stock', and 'active'.
    protected $fillable = [
        'product_category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'active',
    ];

    // The $casts property is used to convert attributes to a specific data type when they are accessed. In this case, the 'price' attribute is cast to a decimal with 2 decimal places, and the 'active' attribute is cast to a boolean.
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    // The category() method defines a relationship between the Product model and the ProductCategory model. It indicates that each product belongs to a single product category. This relationship is established using the belongsTo method, which takes the related model class as an argument.
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}