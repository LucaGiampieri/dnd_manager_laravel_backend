<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene la competenza alla tipologia di arma
            $table->foreignId('character_id')
            ->constrained()
            ->cascadeOnDelete();

            //Tipo di arma in cui il personaggio è competente
            $table->foreignId('weapon_proficiency_id')
            ->constrained('weapon_proficiencies')
            ->cascadeOnDelete();

            //Origine della competenza
            $table->enum('source_type', [
            'class',
            'subclass',
            'race',
            'subrace',
            'background',
            'feat',
            'item',
            'manual',
            'other'
            ])
            ->nullable();

            //ID della fonte
            $table->unsignedBigInteger('source_id')
            ->nullable();

            //Note
            $table->text('notes')
            ->nullable();

            $table->timestamps();

            //Evitiamo che un personaggio abbia due volte la stessa competenza alla tipologia di arma
            $table->unique([
                'character_id',
                'weapon_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_weapon_proficiencies');
    }
};
