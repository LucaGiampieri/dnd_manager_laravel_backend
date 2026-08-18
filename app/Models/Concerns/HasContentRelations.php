<?php

namespace App\Models\Concerns;

use App\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContentRelations
{
    protected static function bootHasContentRelations(): void
    {
        static::deleting(function (Model $model): void {
            if (
                method_exists($model, 'isForceDeleting')
                && ! $model->isForceDeleting()
            ) {
                return;
            }

            $model->outgoingContentRelations()->delete();
            $model->incomingContentRelations()->delete();
        });
    }

    public function outgoingContentRelations(): MorphMany
    {
        return $this->morphMany(
            ContentRelation::class,
            'content'
        );
    }

    public function incomingContentRelations(): MorphMany
    {
        return $this->morphMany(
            ContentRelation::class,
            'related_content'
        );
    }
}
