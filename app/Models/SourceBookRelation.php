<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourceBookRelation extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'source_book_id',
        'related_source_book_id',
        'relation_type',
        'notes',
    ];

    //Converte automaticamente gli identificativi in numeri interi
    protected function casts(): array
    {
        return [
            'source_book_id' => 'integer',
            'related_source_book_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //indica il manuale da cui parte la relazione
    public function sourceBook(): BelongsTo
    {
        return $this->belongsTo(SourceBook::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //indica il manuale verso cui punta la relazione
    public function relatedSourceBook(): BelongsTo
    {
        return $this->belongsTo(
            SourceBook::class,
            'related_source_book_id'
        );
    }
}
