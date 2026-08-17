<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_subclass_histories', function (Blueprint $table) {
            $table->id();

            //Classe del personaggio a cui appartiene la sottoclasse
            $table->foreignId('character_class_level_id')
                ->constrained('character_class_levels')
                ->cascadeOnDelete();

            //Sottoclasse posseduta dal personaggio
            $table->foreignId('subclass_id')
                ->constrained('subclasses')
                ->cascadeOnDelete();

            //Livello di classe in cui è stata ottenuta
            $table->unsignedTinyInteger('acquired_at_class_level')
                ->nullable();

            //Livello di classe in cui è stata sostituita
            $table->unsignedTinyInteger('replaced_at_class_level')
                ->nullable();

            //Indica se questa è la sottoclasse attuale
            $table->boolean('is_current')
                ->default(true);

            //Colonna calcolata: non nulla soltanto per la sottoclasse corrente
            $table->unsignedBigInteger('current_class_level_id')
                ->nullable()
                ->storedAs(
                    'CASE WHEN is_current = 1 THEN character_class_level_id ELSE NULL END'
                );

            //Modalità con cui è stata ottenuta
            $table->enum('acquisition_type', [
                'initial',
                'subclass_change',
                'manual',
                'other',
            ])->default('initial');

            //Motivo del cambio o condizione particolare
            $table->text('reason')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza la ricerca della sottoclasse attuale
            $table->index([
                'character_class_level_id',
                'is_current',
            ], 'character_subclass_histories_current_index');

            //Impedisce di avere due sottoclassi correnti per la stessa classe
            $table->unique(
                'current_class_level_id',
                'character_subclass_histories_one_current_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_subclass_histories');
    }
};
