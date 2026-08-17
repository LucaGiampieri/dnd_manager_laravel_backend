<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_spells', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede l'incantesimo
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Incantesimo concesso dalla sottorazza
            $table->foreignId('spell_id')
                ->constrained()
                ->cascadeOnDelete();

            //Livello del personaggio dal quale viene ottenuto l'incantesimo
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Caratteristica utilizzata per il lancio
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Numero massimo di utilizzi gratuiti
            $table->unsignedTinyInteger('max_uses')
                ->nullable();

            //Quando vengono recuperati gli utilizzi
            $table->enum('recharge', [
                'none',
                'short_rest',
                'long_rest',
                'dawn',
                'other'
            ])
                ->default('none');

            //Indica se può essere lanciato anche utilizzando gli slot del personaggio
            $table->boolean('can_use_spell_slots')
                ->default(false);

            //Livello al quale viene lanciato gratuitamente
            $table->unsignedTinyInteger('cast_at_level')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita duplicati
            $table->unique([
                'subrace_id',
                'spell_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_spells');
    }
};
