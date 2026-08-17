<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_saving_throws', function (Blueprint $table) {

            $table->id();

            //Classe che concede la competenza nel tiro salvezza
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            // Caratteristica del tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Momento in cui viene concessa la competenza nel tiro salvezza
            $table->enum('acquisition_context', [
                'initial',
                'multiclass',
                'both'
            ])
                ->default('initial');

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa competenza nello stesso contesto
            $table->unique([
                'class_id',
                'ability_id',
                'acquisition_context'
            ], 'uq_class_saving_throws_class_id_ability_id_acquisition__dc55f817');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_saving_throws');
    }
};
