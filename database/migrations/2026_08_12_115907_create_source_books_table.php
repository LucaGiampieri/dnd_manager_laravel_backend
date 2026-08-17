<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('source_books', function (Blueprint $table) {

            $table->id();

            //Regolamento principale al quale appartiene la pubblicazione
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Titolo con cui vogliamo visualizzare il manuale
            $table->string('title');

            //Titolo originale, utile soprattutto per manuali tradotti
            $table->string('original_title')->nullable();

            //Slug tecnico stabile da usare nei seeder/import
            $table->string('slug')->unique();

            //Eventuale abbreviazione comunemente usata
            $table->string('abbreviation', 30)->nullable();

            //Tipo generale della pubblicazione
            $table->enum('type', [
                'core_rulebook',
                'supplement',
                'setting',
                'adventure',
                'accessory',
                'other',
            ])->default('supplement');

            //Edizione del gioco a cui appartiene la fonte
            $table->string('edition', 20)->default('5e');

            //Lingua usando un codice semplice
            $table->string('language', 10)->default('it');

            //Editore della pubblicazione
            $table->string('publisher')->nullable();

            //Data di pubblicazione, quando nota
            $table->date('publication_date')->nullable();

            //Indica se la fonte è materiale ufficiale
            $table->boolean('is_official')->default(true);

            //Utile per materiale playtest / prototipi / UA
            $table->boolean('is_playtest')->default(false);

            //Permette di disabilitare una fonte senza cancellarla
            $table->boolean('is_active')->default(true);

            //Annotazioni tecniche o editoriali
            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_books');
    }
};
