<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCar extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type'
    ];
}
