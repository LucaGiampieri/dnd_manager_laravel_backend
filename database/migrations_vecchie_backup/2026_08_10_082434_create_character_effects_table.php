<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effects', function (Blueprint $table) {

            $table->id();

            // ersonaggio sul quale è applicato l'effetto
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome dell'effetto
            $table->string('name');

            //Descrizione dell'effetto
            $table->text('description')
                ->nullable();

            //Origine dell'effetto
            $table->enum('source_type', [
                'spell',
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feat',
                'feature',
                'item',
                'condition',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Indica se l'effetto è attualmente attivo
            $table->boolean('active')
                ->default(true);

            //Indica se questo effetto richiede concentrazione
            $table->boolean('requires_concentration')
                ->default(false);

            //Personaggio che sta mantenendo la concentrazione
            $table->foreignId('concentrator_character_id')
                ->nullable()
                ->constrained('characters')
                ->nullOnDelete();

            //Tipo di durata dell'effetto
            $table->enum('duration_type', [
                'rounds',
                'minutes',
                'hours',
                'until_short_rest',
                'until_long_rest',
                'until_dawn',
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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effects');
    }
};
