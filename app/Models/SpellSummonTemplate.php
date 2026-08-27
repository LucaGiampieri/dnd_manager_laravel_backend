<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpellSummonTemplate extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_summon_id',
        'name',
        'creature_type_id',
        'size_id',
        'description',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_summon_id' => 'integer',
            'creature_type_id' => 'integer',
            'size_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Elimina ordinatamente le forme e i loro stat block dedicati
    protected static function booted(): void
    {
        static::deleting(function (
            SpellSummonTemplate $template
        ): void {
            $template->forms()
                ->get()
                ->each(function (
                    SpellSummonTemplateForm $form
                ): void {
                    $form->delete();
                });
        });
    }

    //Relazione molti-a-uno: ogni template appartiene a una evocazione
    public function spellSummon(): BelongsTo
    {
        return $this->belongsTo(SpellSummon::class);
    }

    //Relazione molti-a-uno: il template dichiara un tipo di creatura
    public function creatureType(): BelongsTo
    {
        return $this->belongsTo(CreatureType::class);
    }

    //Relazione molti-a-uno: il template dichiara una taglia base
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    //Relazione uno-a-molti: un template può avere più forme
    public function forms(): HasMany
    {
        return $this->hasMany(
            SpellSummonTemplateForm::class
        )->orderBy('sort_order');
    }
}
