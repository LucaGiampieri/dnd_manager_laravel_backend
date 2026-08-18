<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class Sense extends Model
{
    use HasSourceReferences;

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
