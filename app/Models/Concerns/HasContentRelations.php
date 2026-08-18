<?php

namespace App\Models\Concerns;

use App\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContentRelations
{
    //Registra la pulizia automatica delle relazioni
    //quando il modello viene eliminato
    protected static function bootHasContentRelations(): void
    {
        //Intercetta l'eliminazione prima che il modello venga rimosso
        static::deleting(function (Model $model): void {
            //Controlla se il modello utilizza le eliminazioni logiche
            if (
                method_exists($model, 'isForceDeleting')
                && ! $model->isForceDeleting()
            ) {
                //Conserva le relazioni se il modello può essere ripristinato
                return;
            }

            //Elimina le relazioni che partono dal contenuto
            $model->outgoingContentRelations()->delete();

            //Elimina le relazioni che arrivano al contenuto
            $model->incomingContentRelations()->delete();
        });
    }

    //Relazione polimorfica uno-a-molti (MorphMany):
    //un contenuto può avere molte relazioni in uscita
    public function outgoingContentRelations(): MorphMany
    {
        return $this->morphMany(
            ContentRelation::class,
            'content'
        );
    }

    //Relazione polimorfica uno-a-molti (MorphMany):
    //un contenuto può avere molte relazioni in entrata
    public function incomingContentRelations(): MorphMany
    {
        return $this->morphMany(
            ContentRelation::class,
            'related_content'
        );
    }
}
