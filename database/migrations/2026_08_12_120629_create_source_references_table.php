<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('source_references', function (Blueprint $table) {

            $table->id();

            //Manuale da cui proviene il contenuto
            $table->foreignId('source_book_id')
                ->constrained('source_books')
                ->cascadeOnDelete();


            //Relazione polimorfica
            $table->morphs('sourceable');


            //Tipo di presenza del contenuto nella fonte

            $table->enum('reference_type', [
                //Il contenuto viene definito normalmente in questo manuale
                'definition',

                // l contenuto viene ristampato senza essere sostanzialmente nuovo
                'reprint',

                //Versione tradotta di un contenuto presente in un'altra fonte
                'translation',

                //Il manuale cita il contenuto, ma non ne fornisce necessariamente la definizione completa
                'reference',

                //Caso particolare non coperto dalle categorie precedenti
                'other',
            ])->default('definition');

            //Pagina iniziale in cui si trova il contenuto
            $table->unsignedSmallInteger('page_start')->nullable();

            //Pagina finale, utile quando il contenuto occupa più pagine
            $table->unsignedSmallInteger('page_end')->nullable();

            //Capitolo o sezione del manuale
            $table->string('section')->nullable();

            //Eventuali informazioni aggiuntive sulla provenienza
            $table->text('notes')->nullable();

            $table->timestamps();

            //Inidice per semplificare la ricerca
            $table->index(
                [
                    'source_book_id',
                    'sourceable_type',
                    'sourceable_id',
                ],
                'source_references_lookup_index'
            );

            //Evita di registrare due volte lo stesso tipo di riferimento
            $table->unique([
                'source_book_id',
                'sourceable_type',
                'sourceable_id',
                'reference_type',
            ], 'source_references_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_references');
    }
};
