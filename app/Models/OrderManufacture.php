<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderManufacture extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'manufacture_id',
        'category_id',
        'comment',
        'price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
