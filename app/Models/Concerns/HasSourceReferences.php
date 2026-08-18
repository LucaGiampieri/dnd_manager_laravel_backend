<?php

namespace App\Models\Concerns;

use App\Models\SourceReference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSourceReferences
{
    use HasContentRelations;

    protected static function bootHasSourceReferences(): void
    {
        static::deleting(function (Model $model): void {
            if (
                method_exists($model, 'isForceDeleting')
                && ! $model->isForceDeleting()
            ) {
                return;
            }

            $model->sourceReferences()->delete();
        });
    }

    public function sourceReferences(): MorphMany
    {
        return $this->morphMany(
            SourceReference::class,
            'sourceable'
        );
    }
}
