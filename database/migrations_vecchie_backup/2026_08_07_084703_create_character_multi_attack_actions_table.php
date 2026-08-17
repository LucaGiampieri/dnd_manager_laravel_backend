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
        Schema::create('character_multi_attack_actions', function (Blueprint $table) {

            $table->id();

            //Sequenza del multiattacco a cui appartiene questa azione
            $table->foreignId('character_multi_attack_sequence_id')
                ->constrained('character_multi_attack_sequences')
                ->cascadeOnDelete();

            //Tipo di attacco/azione eseguita
            $table->enum('action_type', [
                'weapon_attack',
                'magic_attack',
                'other'
            ]);

            //Attacco con arma
            $table->foreignId('character_weapon_attack_id')
                ->nullable()
                ->constrained('character_weapon_attacks')
                ->cascadeOnDelete();

            //Attacco magico
            $table->foreignId('character_magic_attack_id')
                ->nullable()
                ->constrained('character_magic_attacks')
                ->cascadeOnDelete();

            //Nome dell'azione quando action_type = other
            $table->string('name')
                ->nullable();

            //Numero di volte che questa azione viene eseguita nella sequenza
            $table->unsignedTinyInteger('quantity')
                ->default(1);

            //Ordine dell'azione nella sequenza
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuale condizione specifica
            $table->text('condition')
                ->nullable();

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
        Schema::dropIfExists('character_multi_attack_actions');
    }
};
