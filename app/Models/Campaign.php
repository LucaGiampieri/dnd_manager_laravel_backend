<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'owner_user_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni campagna appartiene all'utente che l'ha creata
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_user_id'
        );
    }

    //Relazione molti-a-molti (BelongsToMany):
    //una campagna può rendere disponibili molti manuali
    public function sourceBooks(): BelongsToMany
    {
        return $this->belongsToMany(
            SourceBook::class,
            'campaign_source_books'
        )
            //Utilizza un modello pivot con conversioni dedicate
            ->using(CampaignSourceBook::class)
            ->withPivot([
                'enabled',
                'notes',
            ])
            ->withTimestamps();
    }

    //Relazione molti-a-molti (BelongsToMany):
    //una campagna può attivare molte regole opzionali
    public function optionalRules(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionalRule::class,
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
