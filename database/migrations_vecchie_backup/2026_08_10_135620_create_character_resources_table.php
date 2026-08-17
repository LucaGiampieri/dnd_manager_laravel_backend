<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_resources', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede questa risorsa
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome della risorsa
            $table->string('name');

            //Tipo di origine della risorsa
            $table->enum('source_type', [
                'class_resource',
                'subclass_resource',
                'feature',
                'feat',
                'race',
                'subrace',
                'item',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Quantità massima della risorsa
            $table->unsignedInteger('max_value')
                ->default(0);

            //Quantità attualmente disponibile
            $table->unsignedInteger('current_value')
                ->default(0);

            //Eventuale dado associato alla risorsa
            $table->string('die')
                ->nullable();

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

            //Eventuali note specifiche del personaggio
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di creare due volte la stessa risorsa proveniente dalla stessa fonte per lo stesso personaggio
            $table->unique([
                'character_id',
                'source_type',
                'source_id',
                'name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_resources');
    }
};
