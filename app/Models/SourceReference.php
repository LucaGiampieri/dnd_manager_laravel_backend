<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SourceReference extends Model
{
    //Campi che possono essere valorizzati tramite create o update
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

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'page_start' => 'integer',
            'page_end' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni riferimento appartiene a un solo manuale
    public function sourceBook(): BelongsTo
    {
        return $this->belongsTo(SourceBook::class);
    }

    //Relazione polimorfica molti-a-uno (MorphTo):
    //ogni riferimento appartiene a un contenuto di tipo variabile
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }
}
