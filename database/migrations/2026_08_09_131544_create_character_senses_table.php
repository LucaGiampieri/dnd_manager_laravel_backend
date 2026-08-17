<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_senses', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede il senso
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di senso posseduto
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Portata del senso in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Origine del senso
            $table->string('source_type')
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

            //Lo stesso personaggio non può avere due volte lo stesso senso come stato finale
            $table->unique([
                'character_id',
                'sense_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_senses');
    }
};
