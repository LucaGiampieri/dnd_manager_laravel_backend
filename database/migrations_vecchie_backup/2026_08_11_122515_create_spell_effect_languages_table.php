<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_languages', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede o rimuove temporaneamente una lingua
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Lingua interessata
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            //Operazione effettuata
            $table->enum('operation', [
                'grant',
                'remove'
            ])
                ->default('grant');

            //Indica se l'effetto permette di comprendere la lingua
            $table->boolean('can_understand')
                ->default(true);

            //Indica se permette di parlarla
            $table->boolean('can_speak')
                ->default(true);

            //Indica se permette di leggerla
            $table->boolean('can_read')
                ->default(true);

            //Indica se permette di scriverla
            $table->boolean('can_write')
                ->default(true);

            //Eventuale condizione o limitazione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita che lo stesso spell_effect applichi due volte la stessa operazione sulla stessa lingua
            $table->unique([
                'spell_effect_id',
                'language_id',
                'operation'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_languages');
    }
};
