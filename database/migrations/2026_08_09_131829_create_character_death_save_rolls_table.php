<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_death_save_rolls', function (Blueprint $table) {
            $table->id();

            //Stato dei tiri salvezza a cui appartiene il risultato
            $table->foreignId('character_death_save_id')
                ->constrained('character_death_saves')
                ->cascadeOnDelete();

            //Risultato naturale del d20, se il tiro è stato effettuato
            $table->unsignedTinyInteger('roll')->nullable();

            //Esito già interpretato secondo le regole
            $table->enum('result', [
                'success',
                'failure',
                'critical_success',
                'critical_failure',
                'reset',
                'manual',
            ]);

            //Variazioni applicate ai contatori aggregati
            $table->tinyInteger('successes_delta')->default(0);
            $table->tinyInteger('failures_delta')->default(0);

            $table->timestamp('rolled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index([
                'character_death_save_id',
                'rolled_at',
            ], 'death_save_rolls_timeline_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_death_save_rolls');
    }
};
