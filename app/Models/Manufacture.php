<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(array $array)
 */
class Manufacture extends Model
{
    protected $fillable = [
        'name',
        'web',
        'adress_loading',
        'note',
        'nottypicalproduct',
        'checkmanufacture',
        'date_contract',
        'region',
        'city',
    ];

    public function fedDistRegion(): BelongsTo
    {
        return $this->belongsTo(federalDist::class, 'region', 'id');
    }

    public function fedDistCity(): BelongsTo
    {
        return $this->belongsTo(federalDist::class, 'city', 'id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'manufacture_id', 'id');
    }
}
