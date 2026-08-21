<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignOptionalRule extends Pivot
{
    //Specifica il nome della tabella pivot
    protected $table = 'campaign_optional_rules';

    //Indica che la tabella utilizza un ID incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'campaign_id',
        'optional_rule_id',
        'enabled',
        'configuration',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'campaign_id' => 'integer',
            'optional_rule_id' => 'integer',
            'enabled' => 'boolean',
            'configuration' => 'array',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni configurazione appartiene a una campagna
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni configurazione appartiene a una regola opzionale
    public function optionalRule(): BelongsTo
    {
        return $this->belongsTo(OptionalRule::class);
    }
}
