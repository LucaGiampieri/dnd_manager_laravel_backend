<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_transformations', function (Blueprint $table) {
            $table->id();

            //Personaggio trasformato
            $table->foreignId('character_id')
                ->constrained('characters')
                ->cascadeOnDelete();

            //Regola di trasformazione utilizzata
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Forma specifica selezionata quando prevista
            $table->foreignId('transformation_form_id')
                ->nullable()
                ->constrained('transformation_forms')
                ->nullOnDelete();

            //Stat block effettivamente assunto dal personaggio
            $table->foreignId('creature_stat_block_id')
                ->nullable()
                ->constrained('creature_stat_blocks')
                ->nullOnDelete();

            //Personaggio che ha provocato la trasformazione
            $table->foreignId('source_character_id')
                ->nullable()
                ->constrained('characters')
                ->nullOnDelete();

            //PF del personaggio prima della trasformazione
            $table->unsignedInteger('original_hit_points')
                ->nullable();

            //PF temporanei prima della trasformazione
            $table->unsignedInteger('original_temporary_hit_points')
                ->nullable();

            //PF massimi della forma trasformata
            $table->unsignedInteger('form_max_hit_points')
                ->nullable();

            //PF attuali della forma trasformata
            $table->unsignedInteger('form_current_hit_points')
                ->nullable();

            //PF temporanei della forma trasformata
            $table->unsignedInteger('form_temporary_hit_points')
                ->nullable();

            //Momento di inizio della trasformazione
            $table->timestamp('started_at')
                ->nullable();

            //Momento previsto di fine della trasformazione
            $table->timestamp('expires_at')
                ->nullable();

            //Momento effettivo in cui la trasformazione è terminata
            $table->timestamp('ended_at')
                ->nullable();

            //Indica se la trasformazione è attualmente attiva
            $table->boolean('active')
                ->default(true);

            //Motivo per cui la trasformazione è terminata
            $table->string('end_reason')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero della trasformazione attiva di un personaggio
            $table->index([
                'character_id',
                'active',
            ], 'character_transformations_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_transformations');
    }
};
