<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_multi_attack_sequences', function (Blueprint $table) {

            $table->id();

            //Multiattacco a cui appartiene questa sequenza
            $table->foreignId('character_multi_attack_id')
                ->constrained('character_multi_attacks')
                ->cascadeOnDelete();

            //Nome opzionale della sequenza
            $table->string('name')
                ->nullable();

            //Ordine con cui mostrare le diverse alternative
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuale condizione necessaria per poter scegliere questa sequenza
            $table->text('condition')
                ->nullable();

            //Eventuale descrizione
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_multi_attack_sequences');
    }
};
