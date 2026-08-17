<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_multi_attacks', function (Blueprint $table) {

            $table->id();

            //Personaggio/NPC che possiede il multiattacco
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome visualizzato
            $table->string('name');

            //Origine del multiattacco
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Tipo di azione necessaria per eseguire l'intero multiattacco
            $table->enum('action_type', [
                'action',
                'bonus_action',
                'reaction',
                'legendary_action',
                'lair_action',
                'other'
            ])
                ->default('action');

            //Descrizione generale della sequenza
            $table->text('description')
                ->nullable();

            //Permette di nascondere/disattivare il multiattacco senza cancellarlo
            $table->boolean('active')
                ->default(true);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_multi_attacks');
    }
};
