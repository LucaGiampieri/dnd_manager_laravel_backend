<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubraceSize extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'subrace_id',
        'size_id',
        'notes',
    ];

    //Converte automaticamente gli identificativi in interi
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'size_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione di taglia appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione utilizza una taglia del catalogo
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
