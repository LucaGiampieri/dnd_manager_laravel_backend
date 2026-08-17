<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('rulesets', function (Blueprint $table) {
            $table->id();

            //Chiave tecnica stabile, ad esempio dnd5e_2014 o dnd5e_2024
            $table->string('key')->unique();

            //Nome visualizzato del regolamento
            $table->string('name');

            //Edizione generale del gioco
            $table->string('edition', 30);

            //Revisione o anno di riferimento
            $table->string('revision', 30)->nullable();

            //Descrizione del regolamento
            $table->text('description')->nullable();

            //Permette di disabilitare un regolamento senza cancellarlo
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rulesets');
    }
};
