<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_choices', function (Blueprint $table) {

            $table->id();

            //Background a cui appartiene la scelta
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome della scelta
            $table->string('name');

            //Descrizione della scelta
            $table->text('description')
                ->nullable();

            //Numero di opzioni che il personaggio deve/può scegliere
            $table->unsignedTinyInteger('choose')
                ->default(1);

            //Tipo di scelta
            $table->enum('choice_type', [
                'skill',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'language',
                'ability',
                'item',
                'feature',
                'other'
            ]);

            //Ordine con cui mostrare le scelte durante la creazione del personaggio
            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di creare due scelte con lo stesso nome per lo stesso background
            $table->unique([
                'background_id',
                'name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_choices');
    }
};
