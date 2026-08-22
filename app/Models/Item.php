<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Item extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'canonical_key',
        'version_key',
        'is_legacy',
        'name',
        'item_type_id',
        'description',
        'weight_kg',
        'is_stackable',
        'rarity',
        'is_magical',
        'requires_attunement',
        'requirements',
        'notes',
        'sort_order',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'item_type_id' => 'integer',
            'weight_kg' => 'float',
            'is_legacy' => 'boolean',
            'is_stackable' => 'boolean',
            'is_magical' => 'boolean',
            'requires_attunement' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Registra i valori automatici assegnati ai nuovi oggetti
    protected static function booted(): void
    {
        static::creating(function (Item $item): void {
            //Utilizza la chiave principale come chiave canonica
            //quando non viene fornita esplicitamente
            if (blank($item->canonical_key)) {
                $item->canonical_key = $item->key;
            }

            //Gli oggetti creati dagli utenti appartengono
            //automaticamente alla versione personalizzata
            if (blank($item->version_key)) {
                $item->version_key = 'custom';
            }

            //Le nuove versioni sono attive per impostazione predefinita
            if ($item->is_legacy === null) {
                $item->is_legacy = false;
            }
        });

        //Elimina gli effetti polimorfici quando viene eliminato l'oggetto
        static::deleting(function (Item $item): void {
            //Conserva gli effetti durante una eventuale eliminazione logica
            if (
                method_exists($item, 'isForceDeleting')
                && ! $item->isForceDeleting()
            ) {
                return;
            }

            //Elimina ogni effetto tramite il modello per eseguire
            //anche la pulizia dei suoi riferimenti e delle sue relazioni
            $item->effectDefinitions()
                ->get()
                ->each(function (EffectDefinition $effect): void {
                    $effect->delete();
                });
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni oggetto appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni oggetto appartiene a una tipologia
    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un oggetto può possedere prezzi espressi in diverse valute
    public function costs(): HasMany
    {
        return $this->hasMany(ItemCost::class)
            ->orderBy('currency_id');
    }

    //Relazione uno-a-uno (HasOne):
    //un oggetto utilizzabile come arma possiede un profilo da arma
    public function weaponProfile(): HasOne
    {
        return $this->hasOne(ItemWeaponProfile::class);
    }

    //Relazione uno-a-uno (HasOne):
    //un oggetto utilizzabile come armatura possiede un profilo da armatura
    public function armorProfile(): HasOne
    {
        return $this->hasOne(ItemArmorProfile::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un'arma può essere indicata da competenze specifiche
    public function directWeaponProficiencies(): HasMany
    {
        return $this->hasMany(
            WeaponProficiency::class,
            'item_id'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //un'armatura può essere indicata da competenze specifiche
    public function directArmorProficiencies(): HasMany
    {
        return $this->hasMany(
            ArmorProficiency::class,
            'item_id'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //uno strumento può essere indicato da competenze specifiche
    public function directToolProficiencies(): HasMany
    {
        return $this->hasMany(
            ToolProficiency::class,
            'item_id'
        );
    }

    //Relazione molti-a-molti (BelongsToMany):
    //un'arma può appartenere a più categorie di competenza
    public function weaponProficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            WeaponProficiency::class,
            'weapon_proficiency_items'
        )
            ->using(WeaponProficiencyItem::class)
            ->withPivot([
                'id',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('weapon_proficiencies.sort_order');
    }

    //Relazione molti-a-molti (BelongsToMany):
    //un'armatura può appartenere a più categorie di competenza
    public function armorProficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            ArmorProficiency::class,
            'armor_proficiency_items'
        )
            ->using(ArmorProficiencyItem::class)
            ->withPivot([
                'id',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('armor_proficiencies.sort_order');
    }

    //Relazione molti-a-molti (BelongsToMany):
    //uno strumento può appartenere a più categorie di competenza
    public function toolProficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            ToolProficiency::class,
            'tool_proficiency_items'
        )
            ->using(ToolProficiencyItem::class)
            ->withPivot([
                'id',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('tool_proficiencies.sort_order');
    }

    //Relazione uno-a-uno (HasOne):
    //un oggetto magico può possedere un profilo dedicato
    public function magicProfile(): HasOne
    {
        return $this->hasOne(ItemMagicProfile::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un oggetto può possedere cariche, utilizzi o dosi
    public function resources(): HasMany
    {
        return $this->hasMany(ItemResource::class)
            ->orderBy('sort_order');
    }

    //Relazione polimorfica uno-a-molti (MorphMany):
    //un oggetto può generare molti effetti meccanici
    public function effectDefinitions(): MorphMany
    {
        return $this->morphMany(
            EffectDefinition::class,
            'source'
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //un oggetto può permettere diversi lanci di incantesimo
    public function spellCastings(): HasMany
    {
        return $this->hasMany(ItemSpellCasting::class)
            ->orderBy('sort_order');
    }

    //Relazione molti-a-molti (BelongsToMany):
    //un oggetto può permettere di lanciare molti incantesimi
    public function spells(): BelongsToMany
    {
        return $this->belongsToMany(
            Spell::class,
            'item_spell_castings'
        )
            ->withPivot([
                'id',
                'key',
                'item_resource_id',
                'activation_type',
                'activation_value',
                'resource_cost',
                'cast_at_level',
                'save_dc',
                'spell_attack_bonus',
                'requires_components',
                'requires_concentration',
                'condition',
                'description',
                'sort_order',
                'notes',
            ])
            ->withTimestamps();
    }

    //Relazione uno-a-uno (HasOne):
    //un oggetto può definire le regole di un consumabile
    public function consumableProfile(): HasOne
    {
        return $this->hasOne(ItemConsumableProfile::class);
    }

    //Relazione uno-a-uno (HasOne):
    //un oggetto può definire le capacità di un contenitore
    public function containerProfile(): HasOne
    {
        return $this->hasOne(ItemContainerProfile::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un oggetto magico può essere applicabile a diversi oggetti base
    public function magicApplicabilities(): HasMany
    {
        return $this->hasMany(ItemMagicApplicability::class)
            ->orderBy('sort_order');
    }
}
