<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('features', function (Blueprint $table) {

            $table->id();

            //Nome delle capacità
            $table->string('name');

            //Tipo della capacità
            $table->enum('type', [
               'class',
               'subclass',
               'race',
               'subrace',
               'background',
               'feat',
               'other'
            ]);

            //Livello al quale viene normalmente ottenuta
            $table->unsignedTinyInteger('level')
            ->nullable();

            //Descrizione della capacità
            $table->text('description');

            //Numero massimo di utilizzi della capacità
            $table->unsignedInteger('max_uses')
            ->nullable();

            //Tipo di recupero degli utilizzi della capacità
            $table->enum('recharge', [
                'short_rest',
                'long_rest',
                'dawn',
                'other'
            ])
            ->nullable();

            //Note aggiutnive della capacità
            $table->text('notes')
            ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
