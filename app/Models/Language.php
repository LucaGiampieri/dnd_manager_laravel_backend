<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
        'family',
        'common',
        'selectable',
        'description',
        'category',
        'parent_language_id',
        'language_script_id',
        'typical_speakers',
        'requires_dm_permission',
        'sort_order',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'common' => 'boolean',
            'selectable' => 'boolean',
            'requires_dm_permission' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni lingua può utilizzare un solo alfabeto principale
    public function languageScript(): BelongsTo
    {
        return $this->belongsTo(LanguageScript::class);
    }

    //Relazione ricorsiva molti-a-uno (BelongsTo):
    //ogni dialetto può appartenere a una lingua principale
    public function parentLanguage(): BelongsTo
    {
        return $this->belongsTo(
            Language::class,
            'parent_language_id'
        );
    }

    //Relazione ricorsiva uno-a-molti (HasMany):
    //una lingua principale può avere molti dialetti
    public function dialects(): HasMany
    {
        return $this->hasMany(
            Language::class,
            'parent_language_id'
        );
    }

    //Relazione uno-a-molti:
    //una lingua può essere assegnata a più razze
    public function raceAssignments(): HasMany
    {
        return $this->hasMany(RaceLanguage::class);
    }

    //Relazione uno-a-molti:
    //una lingua può essere assegnata a più sottorazze
    public function subraceAssignments(): HasMany
    {
        return $this->hasMany(SubraceLanguage::class);
    }
}
