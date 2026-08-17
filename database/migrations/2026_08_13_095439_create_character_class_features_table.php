<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_class_features', function (Blueprint $table) {
            $table->id();

            //Classe posseduta dal personaggio
            $table->foreignId('character_class_level_id')
                ->constrained('character_class_levels')
                ->cascadeOnDelete();

            //Feature di classe ottenuta
            $table->foreignId('class_feature_id')
                ->constrained('class_features')
                ->cascadeOnDelete();

            //Feature effettivamente presente sul personaggio
            $table->foreignId('character_feature_id')
                ->constrained('character_feature')
                ->cascadeOnDelete();

            //Modalità con cui è stata ottenuta la feature
            $table->enum('acquisition_type', [
                'automatic',
                'optional',
                'replacement',
                'manual',
            ])->default('automatic');

            //Livello della classe in cui la feature è stata ottenuta
            $table->unsignedTinyInteger('acquired_at_class_level')
                ->nullable();

            //Indica se la feature è attualmente utilizzata
            $table->boolean('active')
                ->default(true);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di assegnare due volte la stessa feature alla stessa progressione di classe
            $table->unique([
                'character_class_level_id',
                'class_feature_id',
            ], 'character_class_features_unique');

            //Una specifica character_feature corrispond a una sola acquisizione di classe
            $table->unique('character_feature_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_class_features');
    }
};
