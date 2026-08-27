<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SpellSummonTemplateScaling extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_summon_template_form_id',
        'key',
        'target_type',
        'target_id',
        'source_type',
        'source_ability_id',
        'operation',
        'source_offset',
        'multiplier',
        'divisor',
        'flat_value',
        'rounding',
        'minimum_source',
        'maximum_source',
        'minimum_result',
        'maximum_result',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_summon_template_form_id' => 'integer',
            'target_id' => 'integer',
            'source_ability_id' => 'integer',
            'source_offset' => 'float',
            'multiplier' => 'float',
            'divisor' => 'float',
            'flat_value' => 'float',
            'minimum_source' => 'float',
            'maximum_source' => 'float',
            'minimum_result' => 'float',
            'maximum_result' => 'float',
            'sort_order' => 'integer',
        ];
    }

    //Impedisce formule matematiche impossibili
    protected static function booted(): void
    {
        static::saving(function (
            SpellSummonTemplateScaling $scaling
        ): void {
            if ((float) $scaling->divisor === 0.0) {
                throw new InvalidArgumentException(
                    'Il divisore della progressione non può essere zero.'
                );
            }
        });
    }

    //Relazione molti-a-uno: ogni crescita appartiene a una forma
    public function form(): BelongsTo
    {
        return $this->belongsTo(
            SpellSummonTemplateForm::class,
            'spell_summon_template_form_id'
        );
    }

    //Relazione molti-a-uno: la crescita può usare una caratteristica
    public function sourceAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'source_ability_id'
        );
    }
}
