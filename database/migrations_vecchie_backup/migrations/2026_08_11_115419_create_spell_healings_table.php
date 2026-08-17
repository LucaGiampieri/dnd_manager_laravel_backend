<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_healings', function (Blueprint $table) {

            $table->id();

            //Incantesimo a cui appartiene questo effetto di cura
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Tipo di beneficio
            $table->enum('healing_type', [
                'healing',
                'temporary_hit_points'
            ])
                ->default('healing');

            //Dadi base
            $table->string('dice')
                ->nullable();

            //Bonus fisso
            $table->integer('bonus')
                ->default(0);

            //Indica se viene aggiunto il modificatore della caratteristica da incantatore
            $table->boolean('applies_ability_modifier')
                ->default(false);

            //Eventuale quantità di bersagli/istanze
            $table->unsignedSmallInteger('quantity')
                ->nullable();

            //Eventuale condizione
            $table->text('condition')
                ->nullable();

            //Ordine nel caso lo spell abbia più componenti di cura
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_healings');
    }
};
