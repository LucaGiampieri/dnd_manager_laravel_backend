<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tool_proficiencies', function (Blueprint $table) {

            $table->id();

            //Nome della competenza nello strumento
            $table->string('name')
                ->unique();

            //Indica se la competenza riguarda:
            //- una categoria di strumenti
            //- uno strumento specifico
            //- una competenza personalizzata/homebrew
            $table->enum('type', [
                'category',
                'specific',
                'custom'
            ])
            ->default('specific');

            // Se la competenza riguarda uno strumento specifico presente nella tabella items, possiamo collegarlo direttamente
            $table->foreignId('item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            //Descrizione opzionale della competenza
            $table->text('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_proficiencies');
    }
};
