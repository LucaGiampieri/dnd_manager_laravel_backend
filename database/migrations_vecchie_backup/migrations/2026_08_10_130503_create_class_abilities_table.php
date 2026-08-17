<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_abilities', function (Blueprint $table) {

            $table->id();

            //Classe a cui è associata la caratteristica
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            //Caratteristica associata alla classe
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Ruolo della caratteristica per la classe
            //Primary: caratteristica principale
            //Secondary: caratteristica secondaria/importante
            $table->enum('role', [
                'primary',
                'secondary'
            ])
                ->default('primary');

            //Ordine di visualizzazione
            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa caratteristica non può essere associata due volte alla stessa classe con lo stesso ruolo
            $table->unique([
                'class_id',
                'ability_id',
                'role'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_abilities');
    }
};
