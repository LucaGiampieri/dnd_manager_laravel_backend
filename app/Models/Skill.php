<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ability_id',
        'name',
        'description',
        'notes',
    ];

    //Relazione molti-a-uno (BelongsTo):
    //ogni abilità è collegata a una sola caratteristica
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
