<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class RaceFeature extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'race_id',
        'feature_id',
        'level',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'feature_id' => 'integer',
            'level' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (
            RaceFeature $raceFeature
        ): void {
            if (
                $raceFeature->level < 1
                || $raceFeature->level > 20
            ) {
                throw new InvalidArgumentException(
                    'Il livello della capacità razziale deve essere '
                    . 'compreso tra 1 e 20.'
                );
            }
        });
    }

    //Relazione molti-a-uno:
    //ogni assegnazione appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    //Relazione molti-a-uno:
    //ogni assegnazione utilizza una capacità
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
