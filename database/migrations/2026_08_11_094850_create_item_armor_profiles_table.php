<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('item_armor_profiles', function (Blueprint $table) {

            $table->id();

            //Oggetto che possiede questo profilo
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Come il valore di CA dell'oggetto viene applicato
            //Set: imposta una CA base
            //Add: aggiunge CA a quella già calcolata
            $table->enum('armor_class_operation', [
                'set',
                'add'
            ])
                ->default('set');

            //Valore base della CA
            $table->integer('armor_class_value');

            //Come viene applicato il modificatore di Destrezza
            //None:nessun modificatore di DES
            //Full: tutto il modificatore di DES
            //Capped: DES applicata fino a un limite
            $table->enum('dexterity_modifier', [
                'none',
                'full',
                'capped'
            ])
                ->default('none');

            //Limite massimo del modificatore di DES
            $table->integer('max_dexterity_bonus')
                ->nullable();

            //Eventuale caratteristica richiesta per utilizzare correttamente l'armatura
            $table->foreignId('requirement_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Valore minimo richiesto
            $table->unsignedTinyInteger('minimum_ability_score')
                ->nullable();

            //Indica se l'armatura impone svantaggio alle prove di furtività
            $table->boolean('stealth_disadvantage')
                ->default(false);

            //Tempo necessario per indossarla, espresso in minuti
            $table->unsignedSmallInteger('don_time_minutes')
                ->nullable();

            //Tempo necessario per rimuoverla, espresso in minuti
            $table->unsignedSmallInteger('doff_time_minutes')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Un item possiede un solo profilo base da armatura/scudo
            $table->unique('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_armor_profiles');
    }
};
