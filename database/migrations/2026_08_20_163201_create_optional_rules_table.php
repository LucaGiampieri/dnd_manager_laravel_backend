<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea il catalogo delle regole opzionali
    public function up(): void
    {
        Schema::create('optional_rules', function (Blueprint $table) {
            $table->id();

            //Regolamento al quale appartiene la regola opzionale
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile utilizzata dal codice
            $table->string('key', 100);

            //Nome mostrato agli utenti
            $table->string('name');

            //Categoria generale della regola
            $table->string('category', 50)
                ->default('general');

            //Spiegazione sintetica e pubblica della regola
            $table->text('description')
                ->nullable();

            //Indica se la regola è normalmente attiva
            $table->boolean('default_enabled')
                ->default(false);

            //Permette di disabilitare una regola senza cancellarla
            $table->boolean('is_active')
                ->default(true);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Annotazioni tecniche o editoriali
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Impedisce chiavi duplicate nello stesso regolamento
            $table->unique(
                [
                    'ruleset_id',
                    'key',
                ],
                'optional_rules_ruleset_key_unique'
            );

            //Velocizza il recupero delle regole per categoria
            $table->index(
                [
                    'ruleset_id',
                    'category',
                    'sort_order',
                ],
                'optional_rules_category_index'
            );
        });
    }

    //Elimina il catalogo delle regole opzionali
    public function down(): void
    {
        Schema::dropIfExists('optional_rules');
    }
};
