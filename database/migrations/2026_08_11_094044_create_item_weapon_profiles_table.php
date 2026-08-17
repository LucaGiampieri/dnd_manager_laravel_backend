<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('item_weapon_profiles', function (Blueprint $table) {

            $table->id();

            //Oggetto che rappresenta l'arma
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Tipo principale di attacco dell'arma
            $table->enum('attack_type', [
                'melee',
                'ranged'
            ]);

            //Portata normale dell'arma in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Portata lunga dell'arma
            $table->decimal('long_range', 10, 3)
                ->nullable();

            //Indica se normalmente viene utilizzata una munizione per effettuare l'attacco
            $table->boolean('uses_ammunition')
                ->default(false);

            //Eventuale capacità del caricatore o numero di colpi prima di dover ricaricare
            $table->unsignedInteger('capacity')
                ->nullable();

            //Eventuali note specifiche sul funzionamento dell'arma
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Un item può avere un solo profilo base da arma
            $table->unique('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_weapon_profiles');
    }
};
