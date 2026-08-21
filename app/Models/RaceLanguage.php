<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaceLanguage extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'race_id',
        'language_id',
        'notes',
    ];

    //Converte gli identificativi nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'language_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno:
    //l'assegnazione appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    //Relazione molti-a-uno:
    //l'assegnazione utilizza una lingua del catalogo
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
