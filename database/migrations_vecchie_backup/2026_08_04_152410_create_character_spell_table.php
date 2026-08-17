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
        Schema::create('character_spell', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede/conosce l'incantesimo
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Incantesimo posseduto dal personaggio
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Origine dalla quale il personaggio ha ottenuto l'incantesimo
            $table->enum('source_type', [
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feat',
                'feature',
                'item',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Indica se il personaggio conosce attualmente l'incantesimo
            $table->boolean('known')
                ->default(false);

            //Indica se l'incantesimo è attualmente preparato
            $table->boolean('prepared')
                ->default(false);

            //Incantesimo sempre conosciuto: non può essere "dimenticato" tramite il normale sistema della classe
            $table->boolean('always_known')
                ->default(false);

            //Incantesimo sempre preparato: non occupa necessariamente uno dei normali incantesimi preparati della classe
            $table->boolean('always_prepared')
                ->default(false);

            //Indica se questo incantesimo conta contro il limite normale degli incantesimi conosciuti/preparati
            $table->boolean('counts_against_limit')
                ->default(true);

            //Caratteristica usata da QUESTA fonte per lanciare l'incantesimo
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Indica se questa versione dell'incantesimo può essere lanciata usando i normali spell slot del personaggio
            $table->boolean('can_use_spell_slots')
                ->default(true);

            //Eventuali note specifiche
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso personaggio può avere lo stesso spell da fonti differenti, ma non due volte dalla stessa identica fonte
            $table->unique([
                'character_id',
                'spell_id',
                'source_type',
                'source_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_spell');
    }
};
