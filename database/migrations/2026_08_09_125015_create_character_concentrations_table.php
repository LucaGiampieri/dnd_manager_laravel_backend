<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_concentrations', function (Blueprint $table) {
            $table->id();

            //Personaggio che mantiene la concentrazione
            $table->foreignId('character_id')
                ->constrained('characters')
                ->cascadeOnDelete();

            //Definizione dell'effetto mantenuto, quando disponibile
            $table->foreignId('effect_definition_id')
                ->nullable()
                ->constrained('effect_definitions')
                ->nullOnDelete();

            //Nome leggibile della concentrazione, conservato come istantanea
            $table->string('name');

            $table->timestamp('started_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            //Un personaggio può mantenere una sola concentrazione alla volta
            $table->unique('character_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_concentrations');
    }
};
