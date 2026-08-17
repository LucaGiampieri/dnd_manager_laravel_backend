<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruleset extends Model
{
    protected $fillable = [
        'key',
        'name',
        'edition',
        'revision',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
