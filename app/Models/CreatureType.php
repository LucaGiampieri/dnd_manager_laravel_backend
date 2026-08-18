<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreatureType extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
