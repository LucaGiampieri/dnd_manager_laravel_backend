<?php

//Questi test leggono i file PHP senza eseguire seeder o accedere al database.
function officialEffectCatalog(): array
{
    static $catalog = null;

    if ($catalog !== null) {
        return $catalog;
    }

    $catalog = [];
    $root = dirname(__DIR__, 2).'/database/data/';

    foreach (['phb_2014_', 'xgte_2017_', 'tcoe_2020_'] as $prefix) {
        $files = array_merge(
            [$root.$prefix.'cantrips.php'],
            glob($root.$prefix.'level_[1-9]_spells.php')
        );
        foreach ($files as $file) {
            //Ogni require ha un ambito separato per le funzioni anonime locali.
            $spells = (static fn (string $path): array => require $path)($file);

            foreach ($spells as $spell) {
                if (isset($catalog[$spell['key']])) {
                    throw new RuntimeException('Chiave duplicata: '.$spell['key']);
                }

                $catalog[$spell['key']] = $spell;
            }
        }
    }

    return $catalog;
}

function officialEffectComponent(string $spellKey, string $collection, string $key): array
{
    foreach (officialEffectCatalog()[$spellKey]['effects'] as $effect) {
        foreach ($effect[$collection] ?? [] as $component) {
            if (($component['key'] ?? null) === $key) {
                return $component;
            }
        }
    }

    throw new RuntimeException("Formula mancante: {$spellKey}/{$collection}/{$key}");
}

//Valuta solo l'aritmetica dichiarata nei dati per verificarne le soglie.
//Non introduce un motore delle regole nell'applicazione.
function officialEffectScaledValue(array $component, string $field, int $source): float
{
    $value = (float) ($component[$field] ?? 0);
    $rules = $component['scalings'] ?? [];
    usort($rules, fn (array $a, array $b): int => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

    foreach ($rules as $rule) {
        if ($rule['target_field'] !== $field) {
            continue;
        }
        if (isset($rule['minimum_source']) && $source < $rule['minimum_source']) {
            continue;
        }
        if (isset($rule['maximum_source']) && $source > $rule['maximum_source']) {
            continue;
        }

        $input = $rule['source_type'] === 'fixed' ? $rule['fixed_value'] : $source;
        $delta = (($input + ($rule['source_offset'] ?? 0))
            * ($rule['multiplier'] ?? 1) / ($rule['divisor'] ?? 1))
            + ($rule['flat_value'] ?? 0);
        $delta = match ($rule['rounding'] ?? 'none') {
            'floor' => floor($delta),
            'ceil' => ceil($delta),
            'round' => round($delta),
            default => $delta,
        };
        if (isset($rule['minimum_result'])) {
            $delta = max($delta, $rule['minimum_result']);
        }
        if (isset($rule['maximum_result'])) {
            $delta = min($delta, $rule['maximum_result']);
        }
        $value = match ($rule['operation'] ?? 'add') {
            'add' => $value + $delta,
            'set' => $delta,
            'multiply' => $value * $delta,
            'minimum' => max($value, $delta),
            'maximum' => min($value, $delta),
        };
    }

    return (float) $value;
}

it('carica tutti i cataloghi senza chiavi o formule incomplete', function () {
    $catalog = officialEffectCatalog();
    expect($catalog)->toHaveCount(477);

    foreach ($catalog as $spell) {
        expect(! empty($spell['effects']) || ! empty($spell['summons']))->toBeTrue();

        foreach ($spell['effects'] ?? [] as $effect) {
            expect($effect['target_scope'] ?? 'target')->not->toBe('self');

            //Lo stesso vincolo applicato da EffectDefinitionForcedMovement:
            //le distanze variabili richiedono special, mai una distanza fittizia.
            foreach ($effect['forced_movements'] ?? [] as $movement) {
                if (($movement['distance'] ?? null) === null) {
                    expect($movement['movement_type'])->toBe('special')
                        ->and($movement['condition'] ?? $movement['notes'] ?? '')
                        ->not->toBeEmpty();
                } else {
                    expect($movement['distance'])->toBeGreaterThan(0);
                }
            }

            foreach (['damages', 'healings', 'roll_modifiers'] as $collection) {
                foreach ($effect[$collection] ?? [] as $formula) {
                    expect(isset($formula['dice_count']))->toBe(isset($formula['die_size']));
                    if (isset($formula['dice_count'])) {
                        expect($formula['dice_count'])->toBeGreaterThan(0)
                            ->and($formula['die_size'])->toBeIn([4, 6, 8, 10, 12, 20, 100]);
                    }
                }
            }
        }
    }
});

it('applica i dadi corretti alle soglie dello slot o del personaggio', function (
    string $spell, string $key, int $source, int $expected
) {
    $damage = officialEffectComponent($spell, 'damages', $key);
    expect(officialEffectScaledValue($damage, 'dice_count', $source))->toBe((float) $expected);
})->with([
    ['fire_bolt', 'damage_fire', 1, 1],
    ['fire_bolt', 'damage_fire', 5, 2],
    ['fire_bolt', 'damage_fire', 11, 3],
    ['fire_bolt', 'damage_fire', 17, 4],
    ['fireball', 'damage_fire', 3, 8],
    ['fireball', 'damage_fire', 9, 14],
    ['hail_of_thorns', 'damage_piercing', 9, 6],
    ['spiritual_weapon', 'damage_force', 3, 1],
    ['spiritual_weapon', 'damage_force', 4, 2],
    ['spiritual_weapon', 'damage_force', 9, 4],
    ['witch_bolt', 'damage_lightning', 5, 5],
    ['witch_bolt', 'sustained_arc', 5, 1],
    ['searing_smite', 'burning', 5, 1],
    ['shadow_blade', 'damage_psychic', 2, 2],
    ['shadow_blade', 'damage_psychic', 3, 3],
    ['shadow_blade', 'damage_psychic', 5, 4],
    ['shadow_blade', 'damage_psychic', 7, 5],
    ['enervation', 'damage_necrotic', 5, 4],
    ['enervation', 'successful_initial_save', 5, 2],
    ['enervation', 'damage_necrotic', 6, 5],
    ['enervation', 'successful_initial_save', 6, 3],
]);

it('separa numero di attacchi cure e tipi alternativi', function () {
    $catalog = officialEffectCatalog();
    $eldritch = $catalog['eldritch_blast']['effects'][0];
    $missile = $catalog['magic_missile']['effects'][0];
    $sleep = $catalog['sleep']['effects'][0]['roll_modifiers'][0];
    $chaos = $catalog['chaos_bolt']['effects'][0]['damages'];

    expect(officialEffectScaledValue($eldritch, 'beam_count', 17))->toBe(4.0)
        ->and($eldritch['damages'][0]['dice_count'])->toBe(1)
        ->and(officialEffectScaledValue($missile, 'dart_count', 9))->toBe(11.0)
        ->and($missile['damages'][0]['flat_bonus'])->toBe(1)
        ->and(officialEffectScaledValue($sleep, 'dice_count', 3))->toBe(9.0)
        ->and($catalog['sleep']['effects'][0]['damages'] ?? [])->toBe([])
        ->and($catalog['enervation']['save_success_damage'])->toBeNull();

    foreach ($chaos as $damage) {
        expect($damage['condition'])->not->toBeEmpty();
        $expected = $damage['die_size'] === 8 ? 2 : 3;
        expect(officialEffectScaledValue($damage, 'dice_count', 3))->toBe((float) $expected);
    }

    $transference = $catalog['life_transference']['effects'];
    expect($transference[0]['target_scope'])->toBe('source')
        ->and($transference[1]['target_scope'])->toBe('target')
        ->and($transference[1]['healings'][0]['modifier_multiplier'])->toBe(2)
        ->and($transference[1]['healings'][0]['dice_count'] ?? null)->toBeNull();
});
