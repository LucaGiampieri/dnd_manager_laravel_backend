<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge il modificatore fisso utilizzato da alcune formule del peso
    public function up(): void
    {
        //Aggiorna sia i tratti della razza sia quelli della sottorazza
        foreach ($this->physicalTraitTables() as $tableName) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    //Quantità fissa aggiunta per ogni punto
                    //del modificatore di altezza, espressa in chilogrammi
                    $table->decimal(
                        'weight_modifier_fixed_kg',
                        10,
                        6
                    )
                        ->nullable()
                        ->after('weight_modifier_unit_kg');
                }
            );
        }
    }

    //Rimuove il modificatore fisso del peso
    public function down(): void
    {
        //Ripristina entrambe le tabelle
        foreach ($this->physicalTraitTables() as $tableName) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table->dropColumn(
                        'weight_modifier_fixed_kg'
                    );
                }
            );
        }
    }

    //Restituisce le tabelle che condividono la formula fisica
    private function physicalTraitTables(): array
    {
        return [
            'race_physical_traits',
            'subrace_physical_traits',
        ];
    }
};
