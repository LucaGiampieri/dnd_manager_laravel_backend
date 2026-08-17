<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_damage_modifiers', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede la resistenza, immunità o vulnerabilità
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica applicata al danno
            $table->enum('modifier', [
                'resistance',
                'immunity',
                'vulnerability'
            ]);

            //Origine della modifica
            $table->enum('source_type', [
                'race',
                'subrace',
                'class',
                'subclass',
                'background',
                'feat',
                'feature',
                'item',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa fonte non può concedere due volte lo stesso modificatore sullo stesso tipo di danno
            $table->unique([
                'character_id',
                'damage_type_id',
                'modifier',
                'source_type',
                'source_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_damage_modifiers');
    }
};
