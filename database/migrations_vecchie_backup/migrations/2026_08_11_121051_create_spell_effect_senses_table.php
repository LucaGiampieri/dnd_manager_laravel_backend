<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_senses', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede o modifica un senso
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Tipo di senso
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Tipo di operazione
            $table->enum('operation', [
                'grant',
                'set',
                'add',
                'remove'
            ])
                ->default('grant');

            //Portata del senso in metri
            $table->float('range')
                ->nullable();

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita che lo stesso effetto applichi due volte la stessa operazione allo stesso senso
            $table->unique([
                'spell_effect_id',
                'sense_id',
                'operation'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_senses');
    }
};
