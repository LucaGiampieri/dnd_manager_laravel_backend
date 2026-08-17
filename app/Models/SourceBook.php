<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceBook extends Model
{
    protected $fillable = [
        'ruleset_id',
        'title',
        'original_title',
        'slug',
        'abbreviation',
        'type',
        'edition',
        'language',
        'publisher',
        'publication_date',
        'is_official',
        'is_playtest',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'is_official' => 'boolean',
            'is_playtest' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

}
