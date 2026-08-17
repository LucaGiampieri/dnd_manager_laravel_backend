<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('equipment_rules', function (Blueprint $table) {
            $table->id();

            //Elemento a cui appartiene la regola
            $table->morphs('equipmentable');

            //Chiave tecnica della regola
            $table->string('key');

            //Equipaggiamento interessato dalla regola
            $table->enum('target_scope', [
                'all',
                'equipped',
                'worn',
                'held',
                'specific_item',
                'special',
            ])->default('all');

            //Oggetto specifico interessato
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();

            //Operazione applicata all'equipaggiamento
            $table->enum('operation', [
                'drop',
                'unequip',
                'disarm',
                'merge',
                'remain_worn',
                'prohibit_use',
                'prohibit_equip',
                'remove',
                'restore',
                'choose',
                'special',
            ]);

            //Indica se l'equipaggiamento continua a funzionare
            $table->boolean('remains_functional')
                ->nullable();

            //Indica se l'equipaggiamento fuso può essere utilizzato
            $table->boolean('merged_equipment_usable')
                ->nullable();

            //Richiede compatibilità con la nuova forma
            $table->boolean('requires_physical_compatibility')
                ->default(false);

            //La scelta viene effettuata separatamente per ogni oggetto
            $table->boolean('choose_per_item')
                ->default(false);

            //Indica se l'equipaggiamento viene ripristinato al termine
            $table->boolean('restore_on_end')
                ->default(true);

            //Condizione necessaria per applicare la regola
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate sullo stesso elemento
            $table->unique([
                'equipmentable_type',
                'equipmentable_id',
                'key',
            ], 'equipment_rules_source_key_unique');

            //Velocizza il recupero delle regole per tipo di equipaggiamento
            $table->index([
                'equipmentable_type',
                'equipmentable_id',
                'target_scope',
            ], 'equipment_rules_target_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_rules');
    }
};
