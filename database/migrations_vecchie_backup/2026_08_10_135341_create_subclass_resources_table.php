<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_resources', function (Blueprint $table) {

            $table->id();

            //Sottoclasse che concede questa risorsa
            $table->foreignId('subclass_id')
                ->constrained('subclasses')
                ->cascadeOnDelete();

            //Nome della risorsa
            $table->string('name');

            //Livello della classe al quale la risorsa viene ottenuta
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Quando la risorsa viene recuperata
            $table->enum('recharge', [
                'none',
                'turn',
                'short_rest',
                'long_rest',
                'dawn',
                'special',
                'other'
            ])
                ->default('none');

            //Descrizione generale della risorsa
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottoclasse non può avere due risorse con lo stesso nome
            $table->unique([
                'subclass_id',
                'name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_resources');
    }
};
