<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasSourceReferences;

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
