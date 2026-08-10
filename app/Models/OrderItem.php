<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_id",
        "product_id",
        "quantity",
        "unit_price"
    ];
    
    // Define the relationship between OrderItem and Order models. Each OrderItem belongs to an Order.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Define the relationship between OrderItem and Product models. Each OrderItem belongs to a Product.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
