<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ability extends Model
{
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
