<?php

namespace Database\Seeders;

use App\Models\MovementType;
use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class MovementCostRuleSeeder extends Seeder
{
    public function run(): void
    {
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        $climbing = MovementType::query()
            ->where('name', 'Scalare')
            ->firstOrFail();

        $swimming = MovementType::query()
            ->where('name', 'Nuotare')
            ->firstOrFail();

        $effect = $ruleset->effectDefinitions()->updateOrCreate(
            [
                'key' => 'core_movement_cost_rules',
            ],
            [
                'name' => 'Regole base dei costi di movimento',
                'application_type' => 'automatic',
                'ends_with_source' => false,
                'description' => 'Definisce i costi aggiuntivi applicati alle principali modalità e situazioni di movimento.',
                'sort_order' => 0,
            ]
        );

        $rules = [
            [
                'key' => 'crawling_extra_cost',
                'context_key' => 'crawling',
                'waived_by_movement_type_id' => null,
                'cost_basis' => 'per_distance',
                'operation' => 'add',
                'value' => '1.000',
                'condition' => 'Si applica mentre la creatura striscia.',
                'sort_order' => 10,
            ],
            [
                'key' => 'difficult_terrain_extra_cost',
                'context_key' => 'difficult_terrain',
                'waived_by_movement_type_id' => null,
                'cost_basis' => 'per_distance',
                'operation' => 'add',
                'value' => '1.000',
                'condition' => 'Si applica quando la creatura attraversa terreno difficile.',
                'sort_order' => 20,
            ],
            [
                'key' => 'climbing_without_speed_extra_cost',
                'context_key' => 'climbing',
                'waived_by_movement_type_id' => $climbing->id,
                'cost_basis' => 'per_distance',
                'operation' => 'add',
                'value' => '1.000',
                'condition' => 'Si applica quando la creatura scala senza utilizzare una velocità di scalata.',
                'sort_order' => 30,
            ],
            [
                'key' => 'swimming_without_speed_extra_cost',
                'context_key' => 'swimming',
                'waived_by_movement_type_id' => $swimming->id,
                'cost_basis' => 'per_distance',
                'operation' => 'add',
                'value' => '1.000',
                'condition' => 'Si applica quando la creatura nuota senza utilizzare una velocità di nuoto.',
                'sort_order' => 40,
            ],
            [
                'key' => 'squeezing_extra_cost',
                'context_key' => 'squeezing',
                'waived_by_movement_type_id' => null,
                'cost_basis' => 'per_distance',
                'operation' => 'add',
                'value' => '1.000',
                'condition' => 'Si applica mentre la creatura si muove stringendosi in uno spazio più piccolo.',
                'sort_order' => 50,
            ],
            [
                'key' => 'standing_from_prone_cost',
                'context_key' => 'standing_from_prone',
                'waived_by_movement_type_id' => null,
                'cost_basis' => 'total_speed_fraction',
                'operation' => 'add',
                'value' => '0.500',
                'condition' => 'Si applica una volta quando la creatura si alza dalla posizione prona.',
                'sort_order' => 60,
            ],
        ];

        foreach ($rules as $rule) {
            $effect->movementCostModifiers()->updateOrCreate(
                [
                    'key' => $rule['key'],
                ],
                [
                    'context_key' => $rule['context_key'],
                    'waived_by_movement_type_id' =>
                        $rule['waived_by_movement_type_id'],
                    'cost_basis' => $rule['cost_basis'],
                    'operation' => $rule['operation'],
                    'value' => $rule['value'],
                    'condition' => $rule['condition'],
                    'sort_order' => $rule['sort_order'],
                ]
            );
        }
    }
}
