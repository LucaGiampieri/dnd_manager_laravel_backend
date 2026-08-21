<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Race extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Valori predefiniti delle razze del regolamento attuale
    protected $attributes = [
        'version_key' => 'phb_2014',
        'is_legacy' => false,
    ];

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'canonical_key',
        'version_key',
        'is_legacy',
        'name',
        'creature_type_id',
        'is_lineage',
        'can_replace_race',
        'selectable',
        'requires_dm_permission',
        'description',
        'typical_alignment',
        'sort_order',
        'notes',
        'ruleset_id',
];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'creature_type_id' => 'integer',
            'is_legacy' => 'boolean',
            'is_lineage' => 'boolean',
            'can_replace_race' => 'boolean',
            'selectable' => 'boolean',
            'requires_dm_permission' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Completa automaticamente i dati prima della creazione
    protected static function booted(): void
    {
        //Usa la chiave del record anche come chiave canonica
        //quando non viene indicata una diversa identità condivisa
        static::creating(function (Race $race): void {
            if (
                $race->canonical_key === null
                || $race->canonical_key === ''
            ) {
                $race->canonical_key = $race->key;
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni razza appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni razza può appartenere a un tipo di creatura
    public function creatureType(): BelongsTo
    {
        return $this->belongsTo(CreatureType::class);
    }

    //Relazione uno-a-uno (HasOne):
    //una razza può possedere una configurazione di tratti fisici
    public function physicalTraits(): HasOne
    {
        return $this->hasOne(RacePhysicalTrait::class);
    }

    //Relazione uno-a-uno (HasOne):
    //una razza può possedere una taglia base automatica
    public function sizeAssignment(): HasOne
    {
        return $this->hasOne(RaceSize::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una razza può possedere più tipi di movimento
    public function movements(): HasMany
    {
        return $this->hasMany(RaceMovement::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una razza può possedere molte sottorazze
    public function subraces(): HasMany
    {
        return $this->hasMany(Subrace::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //una razza può concedere più bonus di caratteristica
    public function abilityBonuses(): HasMany
    {
        return $this->hasMany(RaceAbilityBonus::class)
            ->orderBy('ability_id');
    }

    //Relazione uno-a-molti (HasMany):
    //una razza può definire molte scelte
    public function choices(): HasMany
    {
        return $this->hasMany(RaceChoice::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti:
    //una razza può avere molte assegnazioni di capacità
    public function featureAssignments(): HasMany
    {
        return $this->hasMany(RaceFeature::class);
    }

    //Relazione molti-a-molti:
    //una razza può concedere molte capacità
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            Feature::class,
            'race_features'
        )
            ->withPivot([
                'level',
                'sort_order',
                'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    //Relazione uno-a-molti:
    //una razza può concedere più sensi automatici
    public function senseAssignments(): HasMany
    {
        return $this->hasMany(RaceSense::class);
    }

    //Relazione uno-a-molti:
    //una razza può conoscere automaticamente più lingue
    public function languageAssignments(): HasMany
    {
        return $this->hasMany(RaceLanguage::class);
    }
}
