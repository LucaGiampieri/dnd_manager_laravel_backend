<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasSourceReferences;

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

    protected function casts(): array
    {
        return [
            'common' => 'boolean',
            'selectable' => 'boolean',
            'requires_dm_permission' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function languageScript(): BelongsTo
    {
        return $this->belongsTo(LanguageScript::class);
    }

    public function parentLanguage(): BelongsTo
    {
        return $this->belongsTo(
            Language::class,
            'parent_language_id'
        );
    }

    public function dialects(): HasMany
    {
        return $this->hasMany(
            Language::class,
            'parent_language_id'
        );
    }
}
