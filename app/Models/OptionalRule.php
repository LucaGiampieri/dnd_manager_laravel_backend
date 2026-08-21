<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OptionalRule extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'category',
        'description',
        'default_enabled',
        'is_active',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'default_enabled' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla se la regola è stata attivata nella campagna
    public function isEnabledFor(Campaign $campaign): bool
    {
        return $campaign->optionalRules()
            ->where(
                'optional_rules.id',
                $this->getKey()
            )
            ->wherePivot('enabled', true)
            ->exists();
    }

    //Controlla se la regola è attiva e se il suo manuale
    //principale è disponibile nella campagna
    public function isAvailableFor(Campaign $campaign): bool
    {
        //Una regola disattivata nel catalogo non è utilizzabile
        if (! $this->is_active) {
            return false;
        }

        //La campagna deve avere attivato esplicitamente la regola
        if (! $this->isEnabledFor($campaign)) {
            return false;
        }

        //Recupera i manuali indicati come fonte principale
        $primarySourceBookIds = $this->sourceReferences()
            ->where('is_primary', true)
            ->select('source_book_id');

        //Verifica che almeno una fonte principale sia attiva
        //e disponibile nella campagna
        return $campaign->sourceBooks()
            ->wherePivot('enabled', true)
            ->where('source_books.is_active', true)
            ->whereIn(
                'source_books.id',
                $primarySourceBookIds
            )
            ->exists();
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni regola opzionale appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-molti (BelongsToMany):
    //una regola opzionale può essere configurata in molte campagne
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(
            Campaign::class,
            'campaign_optional_rules'
        )
            //Utilizza un modello pivot con conversioni dedicate
            ->using(CampaignOptionalRule::class)
            ->withPivot([
                'enabled',
                'configuration',
                'notes',
            ])
            ->withTimestamps();
    }
}
