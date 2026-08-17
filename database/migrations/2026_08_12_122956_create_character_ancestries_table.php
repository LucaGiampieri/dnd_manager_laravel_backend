<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_ancestries', function (Blueprint $table) {
            $table->id();

            //Personaggio a cui appartiene l'ascendenza
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Razza o lignaggio posseduto dal personaggio
            $table->foreignId('race_id')
                ->constrained('races')
                ->cascadeOnDelete();

            //Sottorazza eventualmente posseduta
            $table->foreignId('subrace_id')
                ->nullable()
                ->constrained('subraces')
                ->nullOnDelete();

            //Livello del personaggio in cui questa ascendenza è stata ottenuta
            $table->unsignedTinyInteger('acquired_at_level')
                ->nullable();

            //Livello in cui questa ascendenza è stata sostituita
            $table->unsignedTinyInteger('replaced_at_level')
                ->nullable();

            //Indica se questa è l'ascendenza attualmente utilizzata
            $table->boolean('is_current')
                ->default(true);

            //Colonna calcolata: non nulla soltanto per l'ascendenza corrente
            $table->unsignedBigInteger('current_character_id')
                ->nullable()
                ->storedAs(
                    'CASE WHEN is_current = 1 THEN character_id ELSE NULL END'
                );

            //Motivo per cui l'ascendenza è stata ottenuta
            $table->enum('acquisition_type', [
                'character_creation',
                'lineage_replacement',
                'transformation',
                'manual',
                'other',
            ])->default('character_creation');

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza la ricerca delle ascendenze del personaggio
            $table->index([
                'character_id',
                'is_current',
            ]);

            //Impedisce di avere due ascendenze correnti per lo stesso personaggio
            $table->unique(
                'current_character_id',
                'character_ancestries_one_current_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_ancestries');
    }
};
