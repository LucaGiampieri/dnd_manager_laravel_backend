<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SubraceAbilityBonus extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'subrace_id',
        'ability_id',
        'bonus',
        'can_be_reassigned',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'ability_id' => 'integer',
            'bonus' => 'integer',
            'can_be_reassigned' => 'boolean',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce di registrare un modificatore privo di effetto
        static::saving(function (
            SubraceAbilityBonus $abilityBonus
        ) {
            if ($abilityBonus->bonus === 0) {
                throw new InvalidArgumentException(
                    'Il bonus di caratteristica deve essere diverso da zero.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni bonus appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni bonus modifica una caratteristica
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
