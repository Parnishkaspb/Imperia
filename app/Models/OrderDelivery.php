<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDelivery extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "order_id",
        "from",
        "to",
        "buying_price",
        "selling_price",
        "count",
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
