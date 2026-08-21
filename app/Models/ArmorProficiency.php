<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class ArmorProficiency extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'type',
        'item_id',
        'description',
        'sort_order',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'item_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Controlla la coerenza tra il tipo di competenza
        //e l'eventuale armatura collegata direttamente
        static::saving(function (
            ArmorProficiency $proficiency
        ): void {
            //Una competenza specifica deve indicare una armatura
            if (
                $proficiency->type === 'specific'
                && $proficiency->item_id === null
            ) {
                throw new InvalidArgumentException(
                    'Una competenza specifica deve indicare '
                    . 'un’armatura.'
                );
            }

            //Categorie e competenze personalizzate utilizzano
            //le assegnazioni presenti nella tabella pivot
            if (
                $proficiency->type !== 'specific'
                && $proficiency->item_id !== null
            ) {
                throw new InvalidArgumentException(
                    'Soltanto una competenza specifica può indicare '
                    . 'direttamente un’armatura.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni competenza appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //una competenza specifica indica una singola armatura
    public function specificItem(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //una categoria possiede molte assegnazioni di armature
    public function itemAssignments(): HasMany
    {
        return $this->hasMany(
            ArmorProficiencyItem::class
        );
    }

    //Relazione molti-a-molti (BelongsToMany):
    //una categoria di competenza può comprendere molte armature
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'armor_proficiency_items'
        )
            //Utilizza il modello pivot con conversioni dedicate
            ->using(ArmorProficiencyItem::class)
            ->withPivot([
                'id',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('items.sort_order')
            ->orderBy('items.name');
    }
}
