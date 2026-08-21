<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SubraceFeature extends Model
{
    //Valori predefiniti delle assegnazioni
    protected $attributes = [
        'level' => 1,
        'sort_order' => 0,
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'subrace_id',
        'feature_id',
        'level',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'feature_id' => 'integer',
            'level' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (
            SubraceFeature $subraceFeature
        ): void {
            if (
                $subraceFeature->level < 1
                || $subraceFeature->level > 20
            ) {
                throw new InvalidArgumentException(
                    'Il livello della capacità della sottorazza deve '
                    . 'essere compreso tra 1 e 20.'
                );
            }
        });
    }

    //Relazione molti-a-uno:
    //ogni assegnazione appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione molti-a-uno:
    //ogni assegnazione utilizza una capacità
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
