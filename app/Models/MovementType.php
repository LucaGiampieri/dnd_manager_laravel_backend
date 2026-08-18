<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'name',
        'description',
    ];
}
