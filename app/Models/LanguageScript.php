<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LanguageScript extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'key',
        'name',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }
}
