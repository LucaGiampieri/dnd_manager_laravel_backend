<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_spells', function (Blueprint $table) {

            $table->id();

            //Sottoclasse che concede o rende disponibile l'incantesimo
            $table->foreignId('subclass_id')
                ->constrained('subclasses')
                ->cascadeOnDelete();

            //Incantesimo associato alla sottoclasse
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Livello DELLA CLASSE dal quale l'incantesimo diventa disponibile
            $table->unsignedTinyInteger('class_level')
                ->default(1);

            //Come viene concesso l'incantesimo
            $table->enum('grant_type', [
                'spell_list',
                'always_prepared',
                'always_known',
                'granted'
            ])
                ->default('spell_list');

            //Indica se conta contro il normale limite di incantesimi conosciuti/preparati
            $table->boolean('counts_against_limit')
                ->default(false);

            //Eventuali note o regole particolari
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso incantesimo può essere associato una sola volta alla sottoclasse con lo stesso tipo di concessione
            $table->unique([
                'subclass_id',
                'spell_id',
                'grant_type'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_spells');
    }
};
