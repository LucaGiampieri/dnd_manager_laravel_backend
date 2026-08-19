<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class CreatureStatBlockArmorClass extends Model
{
    //Tipologie di Classe Armatura accettate
    public const TYPES = [
        'fixed',
        'natural_armor',
        'armor',
        'unarmored',
        'spell',
        'other',
    ];

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'armor_class',
        'armor_class_type',
        'is_default',
        'description',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'armor_class' => 'integer',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli e la gestione della CA principale
    protected static function booted(): void
    {
        //Controlla e normalizza i dati prima del salvataggio
        static::saving(function (
            CreatureStatBlockArmorClass $armorClass
        ) {
            //Impedisce valori di CA uguali o inferiori a zero
            if ($armorClass->armor_class < 1) {
                throw new InvalidArgumentException(
                    'La Classe Armatura deve essere almeno 1.'
                );
            }

            //Assegna la tipologia predefinita quando non specificata
            if ($armorClass->armor_class_type === null) {
                $armorClass->armor_class_type = 'fixed';
            }

            //Impedisce tipologie di CA non riconosciute
            if (
                ! in_array(
                    $armorClass->armor_class_type,
                    self::TYPES,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'La tipologia della Classe Armatura non è valida.'
                );
            }

            //Rende principale la prima CA inserita nello stat block
            if ($armorClass->getAttribute('is_default') === null) {
                $armorClass->is_default = ! self::query()
                    ->where(
                        'creature_stat_block_id',
                        $armorClass->creature_stat_block_id
                    )
                    ->exists();
            }

            //Rimuove il ruolo principale dalle altre CA
            if ($armorClass->is_default) {
                self::query()
                    ->where(
                        'creature_stat_block_id',
                        $armorClass->creature_stat_block_id
                    )
                    ->when(
                        $armorClass->exists,
                        fn ($query) => $query->where(
                            'id',
                            '!=',
                            $armorClass->id
                        )
                    )
                    ->update([
                        'is_default' => false,
                    ]);
            }
        });

        //Garantisce che rimanga sempre una CA principale
        static::saved(function (
            CreatureStatBlockArmorClass $armorClass
        ) {
            self::ensureDefaultArmorClass(
                $armorClass->creature_stat_block_id
            );
        });

        //Sceglie una nuova CA principale dopo una cancellazione
        static::deleted(function (
            CreatureStatBlockArmorClass $armorClass
        ) {
            self::ensureDefaultArmorClass(
                $armorClass->creature_stat_block_id
            );
        });
    }

    //Assegna come principale la prima CA disponibile
    private static function ensureDefaultArmorClass(
        int $creatureStatBlockId
    ): void {
        //Interrompe l'operazione se esiste già una CA principale
        if (
            self::query()
                ->where(
                    'creature_stat_block_id',
                    $creatureStatBlockId
                )
                ->where('is_default', true)
                ->exists()
        ) {
            return;
        }

        //Recupera la prima CA disponibile secondo l'ordinamento
        $replacement = self::query()
            ->where(
                'creature_stat_block_id',
                $creatureStatBlockId
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        //Promuove la CA trovata senza generare nuovi eventi
        if ($replacement !== null) {
            self::query()
                ->whereKey($replacement->id)
                ->update([
                    'is_default' => true,
                ]);
        }
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni Classe Armatura appartiene a uno stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }
}
