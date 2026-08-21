<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubraceLanguage extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'subrace_id',
        'language_id',
        'notes',
    ];

    //Converte gli identificativi nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'language_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno:
    //l'assegnazione appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione molti-a-uno:
    //l'assegnazione utilizza una lingua del catalogo
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
