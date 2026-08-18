<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CreatureStatBlock extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'monster_id',
        'name',
        'creature_type_id',
        'size_id',
        'alignment',
        'alignment_mode',
        'challenge_rating',
        'proficiency_bonus',
        'description',
        'notes',
        'is_swarm',
        'swarm_component_size_id',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'monster_id' => 'integer',
            'creature_type_id' => 'integer',
            'size_id' => 'integer',
            'challenge_rating' => 'decimal:3',
            'proficiency_bonus' => 'integer',
            'is_swarm' => 'boolean',
            'swarm_component_size_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni stat block può appartenere a un tipo di creatura
    public function creatureType(): BelongsTo
    {
        return $this->belongsTo(CreatureType::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni stat block può avere una taglia principale
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //uno sciame può essere composto da creature di una taglia specifica
    public function swarmComponentSize(): BelongsTo
    {
        return $this->belongsTo(
            Size::class,
            'swarm_component_size_id'
        );
    }

    //Relazione molti-a-molti (BelongsToMany):
    //uno stat block può ammettere più allineamenti
    public function alignments(): BelongsToMany
    {
        return $this->belongsToMany(
            Alignment::class,
            'creature_stat_block_alignments'
        )
    ->using(CreatureStatBlockAlignment::class)
    ->withPivot([
         'is_typical',
         'sort_order',
         'notes',
    ])
    ->withTimestamps()
    ->orderByPivot('sort_order');
    }
}
