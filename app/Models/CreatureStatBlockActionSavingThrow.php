<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatureStatBlockActionSavingThrow extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'creature_stat_block_action_id',
        'key',
        'ability_id',
        'save_dc',
        'success_type',
        'failure_description',
        'success_description',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_action_id' => 'integer',
            'ability_id' => 'integer',
            'save_dc' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno: il tiro salvezza appartiene a una azione
    public function action(): BelongsTo
    {
        return $this->belongsTo(
            CreatureStatBlockAction::class,
            'creature_stat_block_action_id'
        );
    }

    //Relazione molti-a-uno: ogni tiro usa una caratteristica
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
