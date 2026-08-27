<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatureStatBlockAttack extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'creature_stat_block_action_id',
        'key',
        'name',
        'attack_type',
        'attack_kind',
        'attack_bonus',
        'attack_ability_id',
        'reach',
        'range',
        'long_range',
        'target_count',
        'condition',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_action_id' => 'integer',
            'attack_bonus' => 'integer',
            'attack_ability_id' => 'integer',
            'reach' => 'float',
            'range' => 'float',
            'long_range' => 'float',
            'target_count' => 'integer',
        ];
    }

    //Relazione molti-a-uno: ogni attacco appartiene a una azione
    public function action(): BelongsTo
    {
        return $this->belongsTo(
            CreatureStatBlockAction::class,
            'creature_stat_block_action_id'
        );
    }

    //Relazione molti-a-uno: l'attacco può usare una caratteristica
    public function attackAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'attack_ability_id'
        );
    }

    //Relazione uno-a-molti: un attacco può infliggere più danni
    public function damages(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockActionDamage::class
        )->orderBy('sort_order');
    }
}
