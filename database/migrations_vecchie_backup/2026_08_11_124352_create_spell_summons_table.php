<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_summons', function (Blueprint $table) {

            $table->id();

            //Spell che permette questa evocazione
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Nome dell'opzione di evocazione
            $table->string('name');

            //Come vengono scelte le creature
            //Specific: lo spell permette una lista precisa di creature
            //Filter: la creatura viene scelta rispettando criteri come tipo, GS, taglia ecc.
            //Special: regola particolare non rappresentabile direttamente
            $table->enum('selection_type', [
                'specific',
                'filter',
                'special'
            ])
                ->default('specific');

            //Come interpretare quantity
            //Exact: esattamente quella quantità
            //Up_to: fino a quella quantità
            //Special: quantità determinata da una regola particolare dello spell
            $table->enum('quantity_type', [
                'exact',
                'up_to',
                'special'
            ])
                ->default('exact');

            //Numero base di creature evocate
            $table->unsignedSmallInteger('quantity')
                ->nullable();

            //Eventuale GS minimo consentito
            $table->decimal('min_challenge_rating', 6, 3)
                ->nullable();

            //Eventuale GS massimo consentito
            $table->decimal('max_challenge_rating', 6, 3)
                ->nullable();

            //Se true, la creatura evocata segue normalmente gli ordini dell'incantatore
            $table->boolean('controlled_by_caster')
                ->default(true);

            //Se true, la creatura è normalmente amichevole verso l'incantatore
            $table->boolean('friendly_to_caster')
                ->default(true);

            //Se true, l'evocazione termina insieme allo spell
            $table->boolean('ends_with_spell')
                ->default(true);

            //Eventuali criteri
            $table->text('selection_condition')
                ->nullable();

            //Regole su iniziativa, comandi, comportamento e controllo
            $table->text('control_rules')
                ->nullable();

            //Ordine delle diverse opzioni dello stesso spell
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_summons');
    }
};
