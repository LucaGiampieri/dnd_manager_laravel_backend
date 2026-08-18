<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SourceReference extends Model
{
    protected $fillable = [
        'source_book_id',
        'key',
        'reference_type',
        'page_start',
        'page_end',
        'section',
        'notes',
        'is_primary',
        'sort_order',
        'official_text',
    ];

    protected function casts(): array
    {
        return [
            'page_start' => 'integer',
            'page_end' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function sourceBook(): BelongsTo
    {
        return $this->belongsTo(SourceBook::class);
    }

    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }
}
