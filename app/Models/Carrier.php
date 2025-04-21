<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Carrier extends Model
{
    protected $fillable = [
        'who',
        'type_car_id',
        'telephone',
        'email',
        'note',
        'isWorkEarly',
        'isDoc',
    ];


    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeCar::class, 'type_car_id', 'id');
    }
}
