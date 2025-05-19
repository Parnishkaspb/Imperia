<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'status_id',
        'amo_lead',
        'user_id',
    ];

    public function status() : BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
