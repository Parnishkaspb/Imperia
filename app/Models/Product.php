<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'length',
        'width',
        'height',
        'weight',
        'gost',
        'concrete_volume',
        'category_id',
        'nameS'
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
