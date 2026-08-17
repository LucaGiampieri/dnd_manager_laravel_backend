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
        Schema::create('race_physical_traits', function (Blueprint $table) {

            $table->id();

            //Razza a cui appartengono questi tratti fisici
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Età, in anni, alla quale la razza raggiunge normalmente la maturità
            $table->unsignedInteger('maturity_age_years')
                ->nullable();

            //Durata tipica/massima della vita, in anni, indicata dalle regole o dalla descrizione della razza
            $table->unsignedInteger('lifespan_years')
                ->nullable();

            //Altezza minima tipica, in centimetri
            $table->unsignedInteger('min_height_cm')
                ->nullable();

            //Altezza massima tipica, in centimetri
            $table->unsignedInteger('max_height_cm')
                ->nullable();

            //Peso minimo tipico, in chilogrammi
            $table->float('min_weight_kg')
                ->nullable();

            //Peso massimo tipico, in chilogrammi
            $table->float('max_weight_kg')
                ->nullable();

            //Descrizione generale dell'aspetto fisico
            $table->text('appearance')
                ->nullable();

            //Eventuali note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni razza possiede al massimo una configurazione di tratti fisici
            $table->unique('race_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_physical_traits');
    }
};
