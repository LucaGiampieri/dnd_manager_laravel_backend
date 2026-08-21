<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignSourceBook extends Pivot
{
    //Specifica esplicitamente la tabella utilizzata dal modello pivot
    protected $table = 'campaign_source_books';

    //Indica che la tabella possiede una chiave primaria incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'campaign_id',
        'source_book_id',
        'enabled',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'campaign_id' => 'integer',
            'source_book_id' => 'integer',
            'enabled' => 'boolean',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni collegamento appartiene a una campagna
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni collegamento utilizza un manuale
    public function sourceBook(): BelongsTo
    {
        return $this->belongsTo(SourceBook::class);
    }
}
