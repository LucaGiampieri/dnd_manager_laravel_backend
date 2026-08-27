<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpellSummon extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_id',
        'name',
        'selection_type',
        'quantity_type',
        'quantity',
        'min_challenge_rating',
        'max_challenge_rating',
        'controlled_by_caster',
        'friendly_to_caster',
        'ends_with_spell',
        'selection_condition',
        'control_rules',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_id' => 'integer',
            'quantity' => 'integer',
            'min_challenge_rating' => 'float',
            'max_challenge_rating' => 'float',
            'controlled_by_caster' => 'boolean',
            'friendly_to_caster' => 'boolean',
            'ends_with_spell' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Elimina ordinatamente i template per attivare la loro pulizia
    protected static function booted(): void
    {
        static::deleting(function (SpellSummon $summon): void {
            $summon->templates()
                ->get()
                ->each(function (
                    SpellSummonTemplate $template
                ): void {
                    $template->delete();
                });
        });
    }

    //Relazione molti-a-uno: ogni evocazione appartiene a uno spell
    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    //Relazione uno-a-molti: una evocazione può usare più template
    public function templates(): HasMany
    {
        return $this->hasMany(
            SpellSummonTemplate::class
        )->orderBy('sort_order');
    }
}
