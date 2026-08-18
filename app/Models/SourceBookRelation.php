<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceBookRelation extends Model
{
    protected $fillable = [
        'source_book_id',
        'related_source_book_id',
        'relation_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'source_book_id' => 'integer',
            'related_source_book_id' => 'integer',
        ];
    }

    public function sourceBook(): BelongsTo
    {
        return $this->belongsTo(SourceBook::class);
    }

    public function relatedSourceBook(): BelongsTo
    {
        return $this->belongsTo(
            SourceBook::class,
            'related_source_book_id'
        );
    }
}
