<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class CreatureStatBlockAbility extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'ability_id',
        'score',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'ability_id' => 'integer',
            'score' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce punteggi non previsti dal regolamento
        static::saving(function (
            CreatureStatBlockAbility $abilityScore
        ) {
            if (
                $abilityScore->score < 1
                || $abilityScore->score > 30
            ) {
                throw new InvalidArgumentException(
                    'Il punteggio di caratteristica deve essere compreso tra 1 e 30.'
                );
            }
        });
    }

    //Attributo calcolato:
    //restituisce il modificatore derivato dal punteggio
    protected function modifier(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) floor(
                ($this->score - 10) / 2
            )
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni punteggio appartiene a uno stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni punteggio utilizza una caratteristica del catalogo
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
