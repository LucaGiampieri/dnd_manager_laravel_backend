<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpellSummonTemplateForm extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_summon_template_id',
        'creature_stat_block_id',
        'name',
        'description',
        'is_default',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_summon_template_id' => 'integer',
            'creature_stat_block_id' => 'integer',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Elimina lo stat block dedicato quando viene rimossa la forma
    protected static function booted(): void
    {
        static::deleted(function (
            SpellSummonTemplateForm $form
        ): void {
            $form->creatureStatBlock?->delete();
        });
    }

    //Relazione molti-a-uno: ogni forma appartiene a un template
    public function spellSummonTemplate(): BelongsTo
    {
        return $this->belongsTo(SpellSummonTemplate::class);
    }

    //Relazione molti-a-uno: ogni forma usa uno stat block completo
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }

    //Relazione uno-a-molti: una forma possiede regole di crescita
    public function scalings(): HasMany
    {
        return $this->hasMany(
            SpellSummonTemplateScaling::class
        )->orderBy('sort_order');
    }
}
