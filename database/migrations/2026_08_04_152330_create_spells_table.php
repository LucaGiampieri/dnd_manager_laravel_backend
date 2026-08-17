<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spells', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene l'incantesimo
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome dell'incantesimo
            $table->string('name');

            //Livello dello spell
            $table->unsignedTinyInteger('level')
                ->default(0);

            //Scuola di magia
            $table->foreignId('spell_school_id')
                ->constrained('spell_schools')
                ->cascadeOnDelete();

            //TEMPO DI LANCIO:

            //Quantità
            $table->unsignedSmallInteger('casting_time_value')
                ->default(1);

            //Tipologia
            $table->enum('casting_time_type', [
                'action',
                'bonus_action',
                'reaction',
                'round',
                'minute',
                'hour',
                'special'
            ]);

            //Necessario soprattutto per le reaction
            $table->text('casting_trigger')
                ->nullable();

            //Portata
            $table->enum('range_type', [
                'self',
                'touch',
                'distance',
                'sight',
                'unlimited',
                'special'
            ]);

            //Distanza in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //COMPONENTI:

            //Verbali
            $table->boolean('verbal_component')
                ->default(false);

            //Somatici
            $table->boolean('somatic_component')
                ->default(false);

            //Materiali
            $table->boolean('material_component')
                ->default(false);

            //Descrizione del materiale richiesto
            $table->text('material_description')
                ->nullable();

            //Se il materiale viene consumato
            $table->boolean('material_consumed')
                ->default(false);

            //Eventuale costo del materiale
            $table->decimal('material_cost', 10, 2)
                ->nullable();

            //DURATA:

            //Tipologia
            $table->enum('duration_type', [
                'instantaneous',
                'round',
                'minute',
                'hour',
                'day',
                'until_dispelled',
                'permanent',
                'special'
            ]);

            //Componente numerica della durata
            $table->unsignedInteger('duration_value')
                ->nullable();

            //Concentrazione
            $table->boolean('concentration')
                ->default(false);


            //Rituale
            $table->boolean('ritual')
                ->default(false);

            //Se lo spell richiede normalmente un tiro per colpire
            $table->enum('attack_type', [
                'melee',
                'ranged'
            ])
                ->nullable();

            //Caratteristica del TS richiesto al bersaglio
            $table->foreignId('saving_throw_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Cosa succede normalmente al danno quando il TS viene superato
            $table->enum('save_success_damage', [
                'none',
                'half',
                'full'
            ])
                ->nullable();

            //Descrizione completa dello spell
            $table->text('description');

            //Testo specifico sul lancio usando slot di livello superiore
            $table->text('higher_levels')
                ->nullable();

            //Note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'spells_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'spells_ruleset_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spells');
    }
};
