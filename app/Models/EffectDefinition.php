<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EffectDefinition extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
        'application_type',
        'target_scope',
        'ends_with_source',
        'condition',
        'description',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ends_with_source' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Pulisce gli elementi polimorfici prima di eliminare l'effetto
    protected static function booted(): void
    {
        static::deleting(function (
            EffectDefinition $effect
        ): void {
            //I modelli vengono eliminati singolarmente per attivare
            //la pulizia delle loro progressioni polimorfiche
            $effect->damages()
                ->get()
                ->each(function (
                    EffectDefinitionDamage $damage
                ): void {
                    $damage->delete();
                });

            $effect->healings()
                ->get()
                ->each(function (
                    EffectDefinitionHealing $healing
                ): void {
                    $healing->delete();
                });

            //Attiva la pulizia delle progressioni anche per i modificatori.
            $effect->rollModifiers()
                ->get()
                ->each(function (
                    EffectDefinitionRollModifier $modifier
                ): void {
                    $modifier->delete();
                });

            $effect->scalings()->delete();
        });
    }

    //Relazione polimorfica molti-a-uno (MorphTo):
    //ogni effetto può essere definito da una fonte di tipo variabile
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    //Relazione uno-a-molti (HasMany):
    //un effetto può applicare molte modifiche ai costi di movimento
    public function movementCostModifiers(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionMovementCostModifier::class
        );
    }

    //Relazione uno-a-molti: un effetto può infliggere più danni
    public function damages(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionDamage::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //un effetto può applicare diverse formule di guarigione
    public function healings(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionHealing::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //un effetto può modificare diversi tipi di tiro
    public function rollModifiers(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionRollModifier::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti: un effetto può spostare più bersagli
    public function forcedMovements(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionForcedMovement::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti: un effetto può avere regole di durata
    public function durations(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionDuration::class
        )->orderBy('sort_order');
    }

    //Relazione polimorfica: anche un effetto può essere scalabile
    public function scalings(): MorphMany
    {
        return $this->morphMany(
            EffectDefinitionScaling::class,
            'scalable'
        )->orderBy('sort_order');
    }
}
