<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Alignment extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'abbreviation',
        'ethical_axis',
        'moral_axis',
        'description',
        'sort_order',
    ];

    //Converte automaticamente l'ordine in un numero intero
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni allineamento appartiene a un solo regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-molti (BelongsToMany):
    //uno stesso allineamento può essere ammesso da molti stat block
    public function creatureStatBlocks(): BelongsToMany
    {
        return $this->belongsToMany(
            CreatureStatBlock::class,
            'creature_stat_block_alignments'
        )
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
