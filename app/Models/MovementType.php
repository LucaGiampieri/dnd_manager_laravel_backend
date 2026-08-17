<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
}
