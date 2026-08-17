<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_equipment_rules', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene la regola
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Modalità generale di gestione dell'equipaggiamento
            $table->enum('equipment_mode', [
                'drop',
                'merge',
                'remain_worn',
                'choose',
                'special',
            ]);

            //Indica se l'equipaggiamento continua a funzionare
            $table->boolean('remains_functional')
                ->default(false);

            //Indica se l'equipaggiamento assorbito può essere utilizzato
            $table->boolean('merged_equipment_usable')
                ->default(false);

            //Indica se la nuova forma deve poter indossare fisicamente l'oggetto
            $table->boolean('requires_physical_compatibility')
                ->default(true);

            //Indica se la scelta viene fatta separatamente per ogni oggetto
            $table->boolean('choose_per_item')
                ->default(false);

            //Condizione particolare della regola
            $table->text('condition')
                ->nullable();

            //Descrizione delle eccezioni o regole speciali
            $table->text('special_rules')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni trasformazione possiede una sola regola base per la gestione dell'equipaggiamento
            $table->unique(
                'transformation_id',
                'transformation_equipment_rules_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_equipment_rules');
    }
};
