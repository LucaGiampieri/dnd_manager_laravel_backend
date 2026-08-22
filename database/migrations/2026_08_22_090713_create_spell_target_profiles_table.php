<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea la struttura dei bersagli e delle aree degli incantesimi
    public function up(): void
    {
        Schema::create(
            'spell_target_profiles',
            function (Blueprint $table): void {
                $table->id();

                //Incantesimo al quale appartiene il profilo
                $table->foreignId('spell_id')
                    ->unique()
                    ->constrained('spells')
                    ->cascadeOnDelete();

                //Tipologia principale del bersaglio
                $table->enum('target_type', [
                    'self',
                    'creature',
                    'creatures',
                    'object',
                    'objects',
                    'point',
                    'area',
                    'special',
                ]);

                //Numero base di bersagli selezionabili
                $table->unsignedSmallInteger('target_count')
                    ->nullable();

                //Forma geometrica dell'eventuale area
                $table->enum('area_shape', [
                    'cone',
                    'cube',
                    'cylinder',
                    'line',
                    'sphere',
                    'hemisphere',
                    'wall',
                    'emanation',
                    'special',
                ])->nullable();

                //Dimensione principale dell'area espressa in metri
                $table->decimal('area_size_meters', 10, 3)
                    ->nullable();

                //Seconda dimensione, per esempio altezza o larghezza
                $table->decimal(
                    'area_secondary_size_meters',
                    10,
                    3
                )->nullable();

                //Indica se l'incantatore può scegliere sé stesso
                $table->boolean('can_target_self')
                    ->default(false);

                //Indica se l'incantesimo può influenzare oggetti
                $table->boolean('can_target_objects')
                    ->default(false);

                //Indica se il bersaglio deve essere visibile
                $table->boolean('requires_sight')
                    ->default(false);

                //Dettagli non rappresentabili dai campi precedenti
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Velocizza la ricerca per tipologia di bersaglio
                $table->index(
                    'target_type',
                    'spell_target_profiles_target_type_index'
                );

                //Velocizza la ricerca degli incantesimi ad area
                $table->index(
                    'area_shape',
                    'spell_target_profiles_area_shape_index'
                );
            }
        );
    }

    //Rimuove la struttura dei bersagli
    public function down(): void
    {
        Schema::dropIfExists('spell_target_profiles');
    }
};
