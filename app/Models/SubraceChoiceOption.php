<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SubraceChoiceOption extends Model
{
    //Tipi di opzione supportati dalla tabella
    private const OPTION_TYPES = [
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
        'quantity' => 1,
        'sort_order' => 0,
    ];

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'subrace_choice_id',
        'key',
        'option_type',
        'option_id',
        'option_text',
        'ancestry_key',
        'eligibility_condition',
        'value',
        'quantity',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_choice_id' => 'integer',
            'option_id' => 'integer',
            'value' => 'integer',
            'quantity' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce opzioni incomplete o incoerenti
        static::saving(function (SubraceChoiceOption $option) {
            //L'opzione deve utilizzare un tipo supportato
            if (
                ! in_array(
                    $option->option_type,
                    self::OPTION_TYPES,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Il tipo dell’opzione della sottorazza non è valido.'
                );
            }

            //Le opzioni normali devono indicare l'elemento collegato
            if (
                $option->option_type !== 'other'
                && $option->option_id === null
            ) {
                throw new InvalidArgumentException(
                    'L’opzione deve indicare un elemento selezionabile.'
                );
            }

            //Le opzioni libere devono contenere una descrizione
            if (
                $option->option_type === 'other'
                && blank($option->option_text)
            ) {
                throw new InvalidArgumentException(
                    'Un’opzione libera deve contenere un testo.'
                );
            }

            //La quantità deve essere sempre positiva
            if ($option->quantity < 1) {
                throw new InvalidArgumentException(
                    'La quantità dell’opzione deve essere almeno uno.'
                );
            }

            //Recupera la scelta alla quale appartiene l'opzione
            $choice = SubraceChoice::query()
                ->find($option->subrace_choice_id);

            //Il tipo dell'opzione deve corrispondere al tipo della scelta
            if (
                $choice !== null
                && $choice->choice_type !== $option->option_type
            ) {
                throw new InvalidArgumentException(
                    'Il tipo dell’opzione non corrisponde al tipo della scelta.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni opzione appartiene a una scelta della sottorazza
    public function subraceChoice(): BelongsTo
    {
        return $this->belongsTo(SubraceChoice::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //un'opzione di tipo ability può indicare una caratteristica
    public function ability(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'option_id'
        );
    }
}
