<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManufactureCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'manufacture_id',
        'category_id',
        'likethiscategory',
        'comment',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class, 'manufacture_id', 'id');
    }
}
