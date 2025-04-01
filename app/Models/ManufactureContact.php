<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManufactureContact extends Model
{
    protected $fillable = [
        'manufacture_id',
        'name',
        'phone',
        'position',
        'email',
        'active'
    ];

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class);
    }
}
