<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_condition_immunities', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente l'immunità alla condizione
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Condizione alla quale la sottorazza è immune
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

            //La stessa sottorazza non può avere due volte la stessa immunità
            $table->unique([
                'subrace_id',
                'condition_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_condition_immunities');
    }
};
