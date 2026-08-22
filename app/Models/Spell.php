<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spell extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'canonical_key',
        'version_key',
        'is_legacy',
        'name',
        'level',
        'spell_school_id',
        'casting_time_value',
        'casting_time_type',
        'casting_trigger',
        'range_type',
        'range',
        'verbal_component',
        'somatic_component',
        'material_component',
        'material_description',
        'material_consumed',
        'material_cost',
        'duration_type',
        'duration_value',
        'concentration',
        'ritual',
        'attack_type',
        'saving_throw_ability_id',
        'save_success_damage',
        'description',
        'higher_levels',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'level' => 'integer',
            'spell_school_id' => 'integer',
            'casting_time_value' => 'integer',
            'range' => 'float',
            'verbal_component' => 'boolean',
            'somatic_component' => 'boolean',
            'material_component' => 'boolean',
            'material_consumed' => 'boolean',
            'material_cost' => 'integer',
            'duration_value' => 'integer',
            'concentration' => 'boolean',
            'ritual' => 'boolean',
            'saving_throw_ability_id' => 'integer',
            'is_legacy' => 'boolean',
        ];
    }

    //Genera il versionamento e pulisce gli effetti polimorfici
    protected static function booted(): void
    {
        //Assegna valori sicuri agli incantesimi personalizzati
        static::creating(function (Spell $spell): void {
            //Utilizza la chiave tecnica anche come identità canonica
            $spell->canonical_key ??= $spell->key;

            //Gli incantesimi creati dagli utenti appartengono
            //alla versione personalizzata
            $spell->version_key ??= 'custom';

            //Un nuovo incantesimo non è obsoleto
            $spell->is_legacy ??= false;
        });

        //Elimina gli effetti quando viene eliminato l'incantesimo
        static::deleting(function (Spell $spell): void {
            //Conserva gli effetti durante una eliminazione logica
            if (
                method_exists($spell, 'isForceDeleting')
                && ! $spell->isForceDeleting()
            ) {
                return;
            }

            //Elimina ordinatamente ogni effetto dell'incantesimo
            $spell->effectDefinitions()
                ->get()
                ->each(function (EffectDefinition $effect): void {
                    $effect->delete();
                });
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni incantesimo appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni incantesimo appartiene a una scuola di magia
    public function spellSchool(): BelongsTo
    {
        return $this->belongsTo(SpellSchool::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //un incantesimo può richiedere un tiro salvezza
    public function savingThrowAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'saving_throw_ability_id'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //un incantesimo può essere lanciato da molti oggetti
    public function itemCastings(): HasMany
    {
        return $this->hasMany(ItemSpellCasting::class);
    }

    //Relazione molti-a-molti (BelongsToMany):
    //un incantesimo può essere concesso da molti oggetti
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
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

    //Relazione polimorfica uno-a-molti (MorphMany):
    //un incantesimo può produrre molti effetti strutturati
    public function effectDefinitions(): MorphMany
    {
        return $this->morphMany(
            EffectDefinition::class,
            'source'
        );
    }

    //Relazione uno-a-uno (HasOne):
    //ogni incantesimo può possedere un profilo di bersaglio o area
    public function targetProfile(): HasOne
    {
        return $this->hasOne(SpellTargetProfile::class);
    }
}
