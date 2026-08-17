<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_spells', function (Blueprint $table) {

            $table->id();

            //Razza che concede l'incantesimo
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Incantesimo concesso dalla razza
            $table->foreignId('spell_id')
                ->constrained()
                ->cascadeOnDelete();

            //Livello del personaggio dal quale l'incantesimo viene ottenuto
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Caratteristica utilizzata per lanciare questo incantesimo razziale
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

            //Indica se, oltre agli eventuali utilizzi gratuiti, l'incantesimo può essere lanciato usando gli slot del personaggio
            $table->boolean('can_use_spell_slots')
                ->default(false);

            //Livello al quale viene lanciato gratuitamente
            $table->unsignedTinyInteger('cast_at_level')
                ->nullable();

            //Eventuali note o regole particolari
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non deve concedere due volte lo stesso incantesimo allo stesso livello
            $table->unique([
                'race_id',
                'spell_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_spells');
    }
};
