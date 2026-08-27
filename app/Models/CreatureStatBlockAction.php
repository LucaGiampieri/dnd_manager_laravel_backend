<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatureStatBlockAction extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'key',
        'name',
        'action_type',
        'description',
        'trigger',
        'max_uses',
        'recharge_type',
        'recharge_min',
        'recharge_max',
        'legendary_action_cost',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'max_uses' => 'integer',
            'recharge_min' => 'integer',
            'recharge_max' => 'integer',
            'legendary_action_cost' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno: l'azione appartiene a uno stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }

    //Relazione uno-a-molti: una azione può contenere più attacchi
    public function attacks(): HasMany
    {
        return $this->hasMany(CreatureStatBlockAttack::class);
    }

    //Relazione uno-a-molti: una azione può infliggere più danni
    public function damages(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockActionDamage::class
        )->orderBy('sort_order');
    }

    //Relazione uno-a-molti: una azione può richiedere più TS
    public function savingThrows(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockActionSavingThrow::class
        )->orderBy('sort_order');
    }
}
