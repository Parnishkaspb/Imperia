<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 */
class Email extends Model
{
//    public mixed $manufacture_id;
//    public mixed $email;
    protected $fillable = [
        'manufacture_id',
        'email',
    ];
}
