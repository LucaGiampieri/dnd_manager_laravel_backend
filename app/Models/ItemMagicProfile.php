<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemMagicProfile extends Model
{
    //Campi valorizzabili tramite create oppure update
    protected $fillable = [
        'item_id',
        'base_item_id',
        'attunement_requirement',
        'is_cursed',
        'curse_disclosure',
        'destruction_condition',
        'special_rules',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'base_item_id' => 'integer',
            'is_cursed' => 'boolean',
        ];
    }

    //Controlla la coerenza del profilo prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (ItemMagicProfile $profile): void {
            //Recupera l'oggetto proprietario del profilo
            $item = Item::query()->find($profile->item_id);

            //Il profilo deve appartenere a un oggetto esistente
            if ($item === null) {
                throw new InvalidArgumentException(
                    'Il profilo magico deve appartenere '
                    . 'a un oggetto esistente.'
                );
            }

            //Il profilo può essere applicato soltanto a oggetti magici
            if (! $item->is_magical) {
                throw new InvalidArgumentException(
                    'Un profilo magico può appartenere soltanto '
                    . 'a un oggetto contrassegnato come magico.'
                );
            }

            //Un oggetto non può essere la propria versione base
            if (
                $profile->base_item_id !== null
                && $profile->base_item_id === $profile->item_id
            ) {
                throw new InvalidArgumentException(
                    'Un oggetto magico non può utilizzare '
                    . 'se stesso come oggetto base.'
                );
            }

            //Un requisito di sintonia richiede la relativa proprietà
            if (
                $profile->attunement_requirement !== null
                && ! $item->requires_attunement
            ) {
                throw new InvalidArgumentException(
                    'Un requisito di sintonia può essere indicato '
                    . 'soltanto per un oggetto che richiede sintonia.'
                );
            }

            //Una maledizione senza visibilità viene considerata nascosta
            if (
                $profile->is_cursed
                && $profile->curse_disclosure === null
            ) {
                $profile->curse_disclosure = 'hidden';
            }

            //Un oggetto non maledetto non deve avere visibilità maledizione
            if (
                ! $profile->is_cursed
                && $profile->curse_disclosure !== null
            ) {
                throw new InvalidArgumentException(
                    'La visibilità della maledizione può essere indicata '
                    . 'soltanto per un oggetto maledetto.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //il profilo appartiene a un singolo oggetto magico
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //il profilo può derivare da un oggetto comune
    public function baseItem(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'base_item_id'
        );
    }
}
