<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea i componenti materiali dettagliati degli incantesimi
    public function up(): void
    {
        Schema::create(
            'spell_material_components',
            function (Blueprint $table): void {
                $table->id();

                //Incantesimo che richiede il componente
                $table->foreignId('spell_id')
                    ->constrained('spells')
                    ->cascadeOnDelete();

                //Chiave tecnica stabile del componente
                $table->string('key');

                //Nome breve utilizzabile nelle interfacce
                $table->string('name');

                //Descrizione completa del componente richiesto
                $table->text('description')
                    ->nullable();

                //Quantità fisica richiesta dalle regole
                $table->decimal('quantity', 10, 3)
                    ->nullable();

                //Unità della quantità, per esempio pezzo o dose
                $table->string('unit')
                    ->nullable();

                //Costo del componente nella valuta indicata
                $table->decimal('cost_amount', 12, 2)
                    ->nullable();

                //Valuta nella quale viene espresso il costo
                $table->foreignId('currency_id')
                    ->nullable()
                    ->constrained('currencies')
                    ->restrictOnDelete();

                //Indica se il costo rappresenta un valore minimo
                $table->boolean('cost_is_minimum')
                    ->default(false);

                //Indica se il componente viene consumato dal lancio
                $table->boolean('consumed')
                    ->default(false);

                //Indica se un focus può sostituire il componente
                $table->boolean('focus_replaceable')
                    ->default(true);

                //Ordine di visualizzazione nell'incantesimo
                $table->unsignedSmallInteger('sort_order')
                    ->default(0);

                //Note tecniche o precisazioni aggiuntive
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Evita componenti duplicati nello stesso incantesimo
                $table->unique(
                    [
                        'spell_id',
                        'key',
                    ],
                    'spell_material_components_spell_key_unique'
                );

                //Velocizza il recupero ordinato dei componenti
                $table->index(
                    [
                        'spell_id',
                        'sort_order',
                    ],
                    'spell_material_components_order_index'
                );
            }
        );
    }

    //Rimuove la struttura dei componenti materiali
    public function down(): void
    {
        Schema::dropIfExists('spell_material_components');
    }
};
