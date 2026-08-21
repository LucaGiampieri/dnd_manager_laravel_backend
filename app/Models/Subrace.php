<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subrace extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Valori predefiniti delle sottorazze del regolamento attuale
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
        'typical_alignment',
        'is_variant',
        'replaces_race_ability_bonuses',
        'selectable',
        'requires_dm_permission',
        'sort_order',
        'description',
        'notes',
        'race_id',
];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'is_legacy' => 'boolean',
            'is_variant' => 'boolean',
            'replaces_race_ability_bonuses' => 'boolean',
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
        static::creating(function (Subrace $subrace): void {
            if (
                $subrace->canonical_key === null
                || $subrace->canonical_key === ''
            ) {
                $subrace->canonical_key = $subrace->key;
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni sottorazza appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    //Relazione uno-a-uno (HasOne):
    //una sottorazza può possedere una configurazione di tratti fisici
    public function physicalTraits(): HasOne
    {
        return $this->hasOne(
            SubracePhysicalTrait::class
        );
    }

    //Relazione uno-a-uno (HasOne):
    //una sottorazza può definire o sostituire la taglia della razza
    public function sizeAssignment(): HasOne
    {
        return $this->hasOne(SubraceSize::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una sottorazza può aggiungere o sostituire più movimenti
    public function movements(): HasMany
    {
        return $this->hasMany(
            SubraceMovement::class
        );
    }

    //Relazione uno-a-molti (HasMany):
    //una sottorazza può concedere più bonus di caratteristica
    public function abilityBonuses(): HasMany
    {
        return $this->hasMany(SubraceAbilityBonus::class)
            ->orderBy('ability_id');
    }

    //Relazione uno-a-molti (HasMany):
    //una sottorazza può definire molte scelte
    public function choices(): HasMany
    {
        return $this->hasMany(SubraceChoice::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti:
    //una sottorazza può avere molte assegnazioni di capacità
    public function featureAssignments(): HasMany
    {
        return $this->hasMany(SubraceFeature::class);
    }

    //Relazione molti-a-molti:
    //una sottorazza può concedere molte capacità
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            Feature::class,
            'subrace_features'
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
    //una sottorazza può aggiungere o sostituire più sensi
    public function senseAssignments(): HasMany
    {
        return $this->hasMany(SubraceSense::class);
    }

    //Relazione uno-a-molti:
    //una sottorazza può aggiungere lingue a quelle della razza
    public function languageAssignments(): HasMany
    {
        return $this->hasMany(SubraceLanguage::class);
    }
}
