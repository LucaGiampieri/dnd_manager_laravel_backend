<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class SubraceChoice extends Model
{
    //Tipi di scelta supportati dalla tabella
    private const CHOICE_TYPES = [
        'ability',
        'skill',
        'language',
        'weapon_proficiency',
        'armor_proficiency',
        'tool_proficiency',
        'feature',
        'item',
        'size',
        'sense',
        'movement_type',
        'damage_type',
        'other',
    ];

    //Valori utilizzati quando non vengono specificati
    protected $attributes = [
        'choose' => 1,
        'level' => 1,
        'required' => true,
        'sort_order' => 0,
    ];

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
    'subrace_id',
    'key',
    'name',
    'choice_type',
    'replaces_feature_id',
    'choose',
    'level',
    'required',
    'requires_dm_permission',
    'sort_order',
    'description',
    'notes',
];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'replaces_feature_id' => 'integer',
            'requires_dm_permission' => 'boolean',
            'choose' => 'integer',
            'level' => 'integer',
            'required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce configurazioni non valide della scelta
        static::saving(function (SubraceChoice $choice) {
            //La scelta deve utilizzare un tipo supportato
            if (
                ! in_array(
                    $choice->choice_type,
                    self::CHOICE_TYPES,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Il tipo della scelta della sottorazza non è valido.'
                );
            }

            //Deve essere richiesta almeno un'opzione
            if ($choice->choose < 1) {
                throw new InvalidArgumentException(
                    'Il numero di opzioni da scegliere deve essere almeno uno.'
                );
            }

            //Le scelte della sottorazza non possono precedere il primo livello
            if ($choice->level < 1) {
                throw new InvalidArgumentException(
                    'Il livello della scelta deve essere almeno uno.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni scelta appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una scelta può offrire molte opzioni selezionabili
    public function options(): HasMany
    {
        return $this->hasMany(SubraceChoiceOption::class)
            ->orderBy('sort_order');
    }

    //Relazione molti-a-uno:
    //indica la capacità automatica sostituita dalla scelta
    public function replacedFeature(): BelongsTo
    {
        return $this->belongsTo(
            Feature::class,
            'replaces_feature_id'
        );
    }
}
