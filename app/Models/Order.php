<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'status_id',
        'amo_lead',
        'user_id',
        'note',
        'updated_at',
    ];

    public function status() : BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderProducts() : HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function orderManufacture() : HasMany
    {
        return $this->hasMany(OrderManufacture::class, 'order_id');
    }

    public function deliveries() : HasMany
    {
        return $this->hasMany(OrderDelivery::class, 'order_id');
    }
}
