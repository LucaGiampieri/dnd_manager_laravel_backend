<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConditionLevel extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'condition_id',
        'level',
        'name',
        'description',
        'is_terminal',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_terminal' => 'boolean',
        ];
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }
}
