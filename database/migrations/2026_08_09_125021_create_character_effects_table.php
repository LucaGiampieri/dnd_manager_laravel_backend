<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effects', function (Blueprint $table) {

            $table->id();

            //Personaggio sul quale è applicato l'effetto
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Definizione riutilizzabile da cui deriva l'effetto
            //Può essere nulla per un effetto inserito manualmente dal DM
            $table->foreignId('effect_definition_id')
                ->nullable()
                ->constrained('effect_definitions')
                ->nullOnDelete();

            //Nome dell'effetto
            $table->string('name');

            //Descrizione dell'effetto
            $table->text('description')
                ->nullable();

            //Indica se l'effetto è attualmente attivo
            $table->boolean('active')
                ->default(true);

            //Concentrazione che mantiene attivo l'effetto
            $table->foreignId('character_concentration_id')
                ->nullable()
                ->constrained('character_concentrations')
                ->nullOnDelete();

            //Tipo di durata dell'effetto
            $table->enum('duration_type', [
                'instantaneous',
                'turns',
                'rounds',
                'minutes',
                'hours',
                'days',
                'until_short_rest',
                'until_long_rest',
                'until_dawn',
                'until_save_success',
                'until_condition',
                'until_source_ends',
                'permanent',
                'special'
            ])
                ->nullable();

            //Valore della durata
            $table->unsignedInteger('duration_value')
                ->nullable();

            //Numero di round ancora rimanenti
            $table->unsignedInteger('remaining_rounds')
                ->nullable();

            //Momento in cui l'effetto è iniziato
            $table->timestamp('starts_at')
                ->nullable();

            //Momento in cui l'effetto deve terminare, se è possibile determinarlo tramite data/ora
            $table->timestamp('ends_at')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero degli effetti attivi del personaggio
            $table->index([
                'character_id',
                'active',
            ], 'character_effects_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effects');
    }
};
