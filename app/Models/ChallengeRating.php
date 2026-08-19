<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeRating extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'label',
        'numeric_value',
        'proficiency_bonus',
        'experience_points',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'numeric_value' => 'decimal:3',
            'proficiency_bonus' => 'integer',
            'experience_points' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni grado di sfida appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un grado di sfida può essere utilizzato da molti stat block
    public function creatureStatBlocks(): HasMany
    {
        return $this->hasMany(CreatureStatBlock::class);
    }
}
