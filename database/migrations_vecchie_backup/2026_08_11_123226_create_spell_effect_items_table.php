<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_items', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che crea, concede o rimuove l'oggetto
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Oggetto del catalogo interessato
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Operazione effettuata
            $table->enum('operation', [
                'create',
                'grant',
                'remove'
            ])
                ->default('create');

            //Numero di oggetti creati/concessi
            $table->unsignedInteger('quantity')
                ->default(1);

            //Se true, l'oggetto viene considerato equipaggiato quando viene creato/concesso
            $table->boolean('equipped')
                ->default(false);

            //Se true, l'oggetto viene considerato automaticamente sintonizzato
            $table->boolean('attuned')
                ->default(false);

            //Se true, l'oggetto deve sparire quando termina lo spell effect
            $table->boolean('ends_with_spell')
                ->default(true);

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa operazione sullo stesso item all'interno dello stesso spell effect
            $table->unique([
                'spell_effect_id',
                'item_id',
                'operation'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_items');
    }
};
