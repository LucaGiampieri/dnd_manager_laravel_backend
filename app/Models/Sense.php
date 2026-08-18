<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sense extends Model
{
    protected $fillable = [
        'key',
        'name',
        'sort_order',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
