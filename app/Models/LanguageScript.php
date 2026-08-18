<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LanguageScript extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
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

    //Relazione uno-a-molti (HasMany):
    //uno stesso alfabeto può essere utilizzato da molte lingue
    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }
}
