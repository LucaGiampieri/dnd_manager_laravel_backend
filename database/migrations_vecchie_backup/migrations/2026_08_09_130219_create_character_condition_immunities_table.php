<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_condition_immunities', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede l'immunità
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Condizione alla quale il personaggio è immune
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Origine dell'immunità
            $table->enum('source_type', [
                'race',
                'subrace',
                'class',
                'subclass',
                'background',
                'feat',
                'feature',
                'item',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuale condizione o limitazione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa fonte non può concedere due volte la stessa immunità allo stesso personaggio
            $table->unique([
                'character_id',
                'condition_id',
                'source_type',
                'source_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_condition_immunities');
    }
};
