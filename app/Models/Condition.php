<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'description',
        'is_level_based',
        'maximum_level',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'is_level_based' => 'boolean',
            'maximum_level' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni condizione appartiene a un solo regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una condizione progressiva può avere molti livelli ordinati
    public function levels(): HasMany
    {
        return $this->hasMany(ConditionLevel::class)
            ->orderBy('level');
    }
}
