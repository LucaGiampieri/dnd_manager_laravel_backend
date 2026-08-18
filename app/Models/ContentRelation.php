<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentRelation extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'content_type',
        'content_id',
        'related_content_type',
        'related_content_id',
        'relation_type',
        'notes',
    ];

    //Converte automaticamente gli identificativi in numeri interi
    protected function casts(): array
    {
        return [
            'content_id' => 'integer',
            'related_content_id' => 'integer',
        ];
    }

    //Relazione polimorfica molti-a-uno (MorphTo):
    //indica il contenuto da cui parte la relazione
    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    //Relazione polimorfica molti-a-uno (MorphTo):
    //indica il contenuto verso cui punta la relazione
    public function relatedContent(): MorphTo
    {
        return $this->morphTo();
    }
}
