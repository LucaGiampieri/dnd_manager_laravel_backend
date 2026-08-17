<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_condition_immunities', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente l'immunità alla condizione
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Condizione alla quale la razza è immune
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Eventuale condizione o limitazione dell'immunità
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può avere due volte la stessa immunità
            $table->unique([
                'race_id',
                'condition_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_condition_immunities');
    }
};
