<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('source_book_relations', function (Blueprint $table) {

            $table->id();

            //Manuale da cui parte la relazione
            $table->foreignId('source_book_id')
                ->constrained('source_books')
                ->cascadeOnDelete();

            //Manuale a cui la relazione fa riferimento
            $table->foreignId('related_source_book_id')
                ->constrained('source_books')
                ->cascadeOnDelete();

            //Tipo di rapporto editoriale tra i due manuali
            $table->enum('relation_type', [
                //Il primo manuale è una traduzione del secondo
                'translation_of',

                //Il primo manuale è una revisione del secondo
                'revision_of',

                //Il primo manuale è una ristampa del secondo
                'reprint_of',

                //Il primo manuale sostituisce integralmente il secondo come pubblicazione di riferimento
                'supersedes',

                //Il primo manuale deriva editorialmente o meccanicamente dal secondo
                'derived_from',

                //Il primo manuale raccoglie materiale proveniente anche dal secondo
                'compilation_of',

                //Relazione che non rientra nei casi precedenti
                'other',
            ]);

            //Serve quando la relazione riguarda soltanto una parte del manuale o necessita di spiegazioni
            $table->text('notes')->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa relazione tra gli stessi manuali
            $table->unique(
                [
                    'source_book_id',
                    'related_source_book_id',
                    'relation_type',
                ],
                'source_book_relations_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_book_relations');
    }
};
