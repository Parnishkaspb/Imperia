<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    public $timestamps = false;

    protected $fillable = [
      'ip_address',
    ];
}
