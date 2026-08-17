<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_spellcasting', function (Blueprint $table) {

            $table->id();

            //Sottoclasse a cui appartiene questo sistema di lancio degli incantesimi
            $table->foreignId('subclass_id')
                ->constrained('subclasses')
                ->cascadeOnDelete();

            //Caratteristica utilizzata per lanciare gli incantesimi
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Tipo di progressione magica
            $table->enum('spellcasting_type', [
                'full',
                'half',
                'third',
                'pact',
                'custom'
            ]);

            //Indica se i livelli della classe principale contribuiscono agli slot condivisi
            $table->boolean('contributes_to_multiclass_slots')
                ->default(true);

            //Frazione del livello della classe principale usata nel calcolo
            $table->unsignedTinyInteger('multiclass_numerator')
                ->default(1);
            $table->unsignedTinyInteger('multiclass_denominator')
                ->default(3);

            //Regola di arrotondamento del contributo
            $table->enum('multiclass_rounding', [
                'floor',
                'ceil',
                'round',
            ])->default('floor');

            //La sottoclasse prepara gli incantesimi
            $table->boolean('prepared_spells')
                ->default(false);

            //La sottoclasse utilizzauna lista di incantesimi conosciuti
            $table->boolean('known_spells')
                ->default(false);

            //Può lanciare incantesimi come rituali
            $table->boolean('ritual_casting')
                ->default(false);

            //Può utilizzare un focus da incantatore
            $table->boolean('spellcasting_focus')
                ->default(false);

            //Descrizione delle eventuali regole particolari di spellcasting
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una sottoclasse può avere una sola configurazione principale di spellcasting
            $table->unique('subclass_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_spellcasting');
    }
};
