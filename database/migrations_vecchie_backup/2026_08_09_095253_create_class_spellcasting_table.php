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
        Schema::create('class_spellcasting', function (Blueprint $table) {

            $table->id();

            //Classe a cui appartiene il sistema di lancio incatesimi
            $table->foreignId('class_id')
            ->constrained()
            ->cascadeOnDelete();

            //Caratteristica utilizzata dalla classe per lanciare gli incantesimi
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Tipo di incantatore
            $table->enum('spellcasting_type', [
                'full',
                'half',
                'third',
                'pact'
            ]);

            //La classe prepara gli incantesimi
            $table->boolean('prepared_spells')
            ->default(false);

            //La classe conosce un numero fisso di incantesimi
            $table->boolean('known_spells')
            ->default(false);

            //Può lanciare incantesimi come rituale
            $table->boolean('ritual_casting')
            ->default(false);

            //Può usare un focus da incantatore
            $table->boolean('spellcasting_focus')
            ->default(false);

            //Descrizione del sistema di lancio
            $table->text('description')
            ->nullable();

            $table->timestamps();

            //Una classe normalmente ha un solo sistema di lancio
            $table->unique('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_spellcasting');
    }
};
