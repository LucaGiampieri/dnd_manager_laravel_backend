<?php

namespace App\Models\Concerns;

use App\Models\SourceReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSourceReferences
{
    //Aggiunge anche le relazioni in entrata e in uscita
    //verso gli altri contenuti ufficiali
    use HasContentRelations;

    //Registra la pulizia automatica dei riferimenti
    //quando il modello viene eliminato
    protected static function bootHasSourceReferences(): void
    {
        //Intercetta l'eliminazione prima che il modello venga rimosso
        static::deleting(function (Model $model): void {
            //Controlla se il modello utilizza le eliminazioni logiche
            if (
                method_exists($model, 'isForceDeleting')
                && ! $model->isForceDeleting()
            ) {
                //Conserva i riferimenti se il modello può essere ripristinato
                return;
            }

            //Elimina tutti i riferimenti ai manuali del contenuto
            $model->sourceReferences()->delete();
        });
    }

    //Relazione polimorfica uno-a-molti (MorphMany):
    //un contenuto può avere molti riferimenti a manuali e pagine
    public function sourceReferences(): MorphMany
    {
        return $this->morphMany(
            SourceReference::class,
            'sourceable'
        );
    }
}
