<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'challenge_rating_id',
        'experience_points_override',
        'proficiency_bonus_override',
        'alignment',
        'alignment_mode',
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
            'challenge_rating_id' => 'integer',
            'experience_points_override' => 'integer',
            'proficiency_bonus_override' => 'integer',
            'is_swarm' => 'boolean',
            'swarm_component_size_id' => 'integer',
        ];
    }

    //Attributo calcolato:
    //usa il bonus personalizzato oppure quello previsto dal grado di sfida
    protected function proficiencyBonus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->proficiency_bonus_override
                ?? $this->challengeRating?->proficiency_bonus
        );
    }

    //Attributo calcolato:
    //usa i PE personalizzati oppure quelli previsti dal grado di sfida
    protected function experiencePoints(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->experience_points_override
                ?? $this->challengeRating?->experience_points
        );
    }

    //Attributo calcolato:
    //restituisce il valore della Classe Armatura principale
    protected function armorClass(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                $this->defaultArmorClass?->armor_class
        );
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
    //ogni stat block può utilizzare un grado di sfida ufficiale
    public function challengeRating(): BelongsTo
    {
        return $this->belongsTo(ChallengeRating::class);
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

    //Relazione uno-a-molti (HasMany):
    //uno stat block possiede un punteggio per ogni caratteristica
    public function abilityScores(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockAbility::class
        );
    }

    //Relazione uno-a-molti (HasMany):
    //uno stat block può possedere più Classi Armatura
    public function armorClasses(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockArmorClass::class
        )
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    //Attributo calcolato:
    //restituisce i Punti Ferita medi dello stat block
    protected function averageHitPoints(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                $this->hitPoints?->effective_average_hit_points
        );
    }

    //Attributo calcolato:
    //restituisce la formula dei Dadi Vita dello stat block
    protected function hitDiceFormula(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                $this->hitPoints?->hit_dice_formula
        );
    }

    //Relazione uno-a-uno (HasOne):
    //uno stat block utilizza una sola Classe Armatura principale
    public function defaultArmorClass(): HasOne
    {
        return $this->hasOne(
            CreatureStatBlockArmorClass::class
        )
            ->where('is_default', true);
    }

    //Relazione uno-a-uno (HasOne):
    //uno stat block possiede una sola definizione base dei Punti Ferita
    public function hitPoints(): HasOne
    {
        return $this->hasOne(
            CreatureStatBlockHitPoint::class
        );
    }

    //Relazione uno-a-molti:
    //uno stat block può possedere più modalità di movimento
    public function movements(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockMovement::class
        );
    }

    //Relazione uno-a-molti:
    //uno stat block può possedere azioni, reazioni e azioni bonus
    public function actions(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockAction::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti:
    //uno stat block può essere usato da una forma evocata
    public function summonTemplateForms(): HasMany
    {
        return $this->hasMany(
            SpellSummonTemplateForm::class
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
            //Utilizza un modello pivot con conversioni dedicate
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
