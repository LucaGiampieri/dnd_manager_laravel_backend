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
        Schema::create('subrace_physical_traits', function (Blueprint $table) {

            $table->id();

            //Sottorazza a cui appartengono questi tratti fisici
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Età di maturità, in anni
            $table->unsignedInteger('maturity_age_years')
                ->nullable();

            //Durata della vita, in anni
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

            //Descrizione dell'aspetto fisico della sottorazza
            $table->text('appearance')
                ->nullable();

            // ventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni sottorazza possiede al massimo una configurazione di tratti fisici
            $table->unique('subrace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subrace_physical_traits');
    }
};
