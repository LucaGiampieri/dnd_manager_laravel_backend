<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentRelation extends Model
{
    protected $fillable = [
        'content_type',
        'content_id',
        'related_content_type',
        'related_content_id',
        'relation_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'content_id' => 'integer',
            'related_content_id' => 'integer',
        ];
    }

    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    public function relatedContent(): MorphTo
    {
        return $this->morphTo();
    }
}
