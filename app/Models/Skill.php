<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'ability_id',
        'name',
        'description',
        'notes',
    ];

    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
