<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SpellTargetProfile extends Model
{
    //Forme di area riconosciute dall'applicazione
    public const AREA_SHAPES = [
        'cone',
        'cube',
        'cylinder',
        'line',
        'sphere',
        'hemisphere',
        'wall',
        'emanation',
        'square',
        'rectangle',
        'circle',
        'special',
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_id',
        'target_type',
        'target_count',
        'area_shape',
        'area_size_meters',
        'area_secondary_size_meters',
        'can_target_self',
        'can_target_objects',
        'requires_sight',
        'notes',
    ];

    //Converte i valori del database nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_id' => 'integer',
            'target_count' => 'integer',
            'area_size_meters' => 'float',
            'area_secondary_size_meters' => 'float',
            'can_target_self' => 'boolean',
            'can_target_objects' => 'boolean',
            'requires_sight' => 'boolean',
        ];
    }

    //Controlla la coerenza del profilo prima del salvataggio
    protected static function booted(): void
    {
        static::saving(
            function (SpellTargetProfile $profile): void {
                //Il numero dei bersagli deve essere positivo
                if (
                    $profile->target_count !== null
                    && $profile->target_count < 1
                ) {
                    throw new InvalidArgumentException(
                        'Il numero dei bersagli deve essere positivo.'
                    );
                }

                $isArea = $profile->target_type === 'area';

                $hasAreaShape =
                    $profile->area_shape !== null;

                //La forma deve appartenere al catalogo supportato
                if (
                    $hasAreaShape
                    && ! in_array(
                        $profile->area_shape,
                        self::AREA_SHAPES,
                        true
                    )
                ) {
                    throw new InvalidArgumentException(
                        'La forma dell’area indicata non è supportata.'
                    );
                }

                $hasAreaSize =
                    $profile->area_size_meters !== null;

                $hasSecondaryAreaSize =
                    $profile->area_secondary_size_meters !== null;

                //Le aree normali devono indicare forma e dimensione.
                //Le aree speciali possono non avere una misura unica,
                //ma devono essere spiegate dettagliatamente nelle note.
                if (
                    $isArea
                    && (
                        ! $hasAreaShape
                        || (
                            $profile->area_shape !== 'special'
                            && ! $hasAreaSize
                        )
                    )
                ) {
                    throw new InvalidArgumentException(
                        'Un incantesimo ad area deve indicare una forma '
                        . 'e, salvo le aree speciali, una dimensione.'
                    );
                }

                //Una area speciale senza dimensioni deve essere descritta
                if (
                    $isArea
                    && $profile->area_shape === 'special'
                    && blank($profile->notes)
                ) {
                    throw new InvalidArgumentException(
                        'Una area speciale deve essere descritta nelle note.'
                    );
                }

                //I campi dell'area non appartengono ai bersagli normali
                if (
                    ! $isArea
                    && (
                        $hasAreaShape
                        || $hasAreaSize
                        || $hasSecondaryAreaSize
                    )
                ) {
                    throw new InvalidArgumentException(
                        'Le dimensioni dell’area possono essere usate '
                        . 'soltanto con il bersaglio area.'
                    );
                }

                //La dimensione principale deve essere positiva
                if (
                    $hasAreaSize
                    && $profile->area_size_meters <= 0
                ) {
                    throw new InvalidArgumentException(
                        'La dimensione principale dell’area '
                        . 'deve essere positiva.'
                    );
                }

                //La seconda dimensione deve essere positiva
                if (
                    $hasSecondaryAreaSize
                    && $profile->area_secondary_size_meters <= 0
                ) {
                    throw new InvalidArgumentException(
                        'La seconda dimensione dell’area '
                        . 'deve essere positiva.'
                    );
                }
            }
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni profilo appartiene a un solo incantesimo
    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }
}
