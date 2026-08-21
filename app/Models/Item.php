<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
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
            'weight_kg' => 'decimal:3',
            'is_stackable' => 'boolean',
            'is_magical' => 'boolean',
            'requires_attunement' => 'boolean',
            'sort_order' => 'integer',
        ];
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
    //un'arma può essere indicata direttamente da competenze specifiche
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
            //Utilizza il modello pivot con conversioni dedicate
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
            //Utilizza il modello pivot con conversioni dedicate
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
            //Utilizza il modello pivot con conversioni dedicate
            ->using(ToolProficiencyItem::class)
            ->withPivot([
                'id',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('tool_proficiencies.sort_order');
    }
}
