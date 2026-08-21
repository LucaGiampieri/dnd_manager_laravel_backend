<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge le formule ufficiali di generazione fisica
    public function up(): void
    {
        //Applica gli stessi campi a razze e sottorazze
        foreach ($this->physicalTraitTables() as $tableName) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    //Altezza utilizzata come punto di partenza
                    $table->decimal('base_height_cm', 8, 3)
                        ->nullable()
                        ->after('max_weight_kg');

                    //Numero di dadi del modificatore di altezza
                    $table->unsignedTinyInteger(
                        'height_modifier_dice_count'
                    )
                        ->nullable()
                        ->after('base_height_cm');

                    //Numero di facce dei dadi del modificatore
                    $table->unsignedSmallInteger(
                        'height_modifier_die_size'
                    )
                        ->nullable()
                        ->after('height_modifier_dice_count');

                    //Centimetri aggiunti per ogni punto ottenuto
                    $table->decimal(
                        'height_modifier_unit_cm',
                        8,
                        3
                    )
                        ->nullable()
                        ->after('height_modifier_die_size');

                    //Peso utilizzato come punto di partenza
                    $table->decimal('base_weight_kg', 10, 3)
                        ->nullable()
                        ->after('height_modifier_unit_cm');

                    //Numero di dadi del modificatore di peso
                    $table->unsignedTinyInteger(
                        'weight_modifier_dice_count'
                    )
                        ->nullable()
                        ->after('base_weight_kg');

                    //Numero di facce dei dadi del modificatore
                    $table->unsignedSmallInteger(
                        'weight_modifier_die_size'
                    )
                        ->nullable()
                        ->after('weight_modifier_dice_count');

                    //Chilogrammi aggiunti per ogni punto ottenuto
                    $table->decimal(
                        'weight_modifier_unit_kg',
                        10,
                        6
                    )
                        ->nullable()
                        ->after('weight_modifier_die_size');

                    //Indica se il peso usa anche il tiro dell'altezza
                    $table->boolean(
                        'weight_uses_height_modifier'
                    )
                        ->default(true)
                        ->after('weight_modifier_unit_kg');
                }
            );
        }
    }

    //Rimuove le formule di generazione fisica
    public function down(): void
    {
        //Ripristina entrambe le tabelle alla struttura precedente
        foreach ($this->physicalTraitTables() as $tableName) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table->dropColumn([
                        'base_height_cm',
                        'height_modifier_dice_count',
                        'height_modifier_die_size',
                        'height_modifier_unit_cm',
                        'base_weight_kg',
                        'weight_modifier_dice_count',
                        'weight_modifier_die_size',
                        'weight_modifier_unit_kg',
                        'weight_uses_height_modifier',
                    ]);
                }
            );
        }
    }

    //Restituisce le tabelle che condividono la stessa struttura
    private function physicalTraitTables(): array
    {
        return [
            'race_physical_traits',
            'subrace_physical_traits',
        ];
    }
};
