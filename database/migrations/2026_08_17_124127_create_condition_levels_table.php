<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('condition_levels', function (Blueprint $table) {
            $table->id();

            //Condizione alla quale appartiene il livello
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Numero del livello
            $table->unsignedTinyInteger('level');

            //Nome visualizzato del livello
            $table->string('name');

            //Descrizione dell’effetto applicato a questo livello
            $table->text('description');

            //Indica se il livello produce un esito terminale
            $table->boolean('is_terminal')
                ->default(false);

            $table->timestamps();

            //Una condizione può avere una sola definizione per ogni livello
            $table->unique([
                'condition_id',
                'level',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_levels');
    }
};
