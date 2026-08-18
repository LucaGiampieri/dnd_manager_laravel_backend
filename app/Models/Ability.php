<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ability extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'name',
        'short_name',
        'description',
    ];

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }
}
