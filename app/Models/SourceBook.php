<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SourceBook extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'title',
        'original_title',
        'slug',
        'abbreviation',
        'type',
        'edition',
        'language',
        'publisher',
        'publication_date',
        'is_official',
        'is_playtest',
        'is_active',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'is_official' => 'boolean',
            'is_playtest' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni manuale appartiene a un solo regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un manuale può essere citato da molti riferimenti
    public function sourceReferences(): HasMany
    {
        return $this->hasMany(SourceReference::class);
    }

    //Relazione molti-a-molti (BelongsToMany):
    //un manuale può essere reso disponibile in molte campagne
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(
            Campaign::class,
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

    //Relazione uno-a-molti (HasMany):
    //un manuale può avere molte relazioni editoriali in uscita
    public function outgoingSourceBookRelations(): HasMany
    {
        return $this->hasMany(
            SourceBookRelation::class,
            'source_book_id'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //un manuale può avere molte relazioni editoriali in entrata
    public function incomingSourceBookRelations(): HasMany
    {
        return $this->hasMany(
            SourceBookRelation::class,
            'related_source_book_id'
        );
    }
}
