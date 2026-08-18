<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionLevel extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'condition_id',
        'level',
        'name',
        'description',
        'is_terminal',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_terminal' => 'boolean',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni livello appartiene a una sola condizione progressiva
    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }
}
