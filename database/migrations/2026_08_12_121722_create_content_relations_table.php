<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('content_relations', function (Blueprint $table) {

            $table->id();

            //Contenuto da cui parte la relazione
            $table->morphs('content');

            //Contenuto collegato
            $table->morphs('related_content');

            //Tipo di relazione tra i due contenuti
            $table->enum('relation_type', [
                'revision_of',
                'supersedes',
                'reprint_of',
                'translation_of',
                'variant_of',
                'derived_from',
                'other',
            ]);

            //Note aggiuntive sulla relazione
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa relazione
            $table->unique([
                'content_type',
                'content_id',
                'related_content_type',
                'related_content_id',
                'relation_type',
            ], 'content_relations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_relations');
    }
};
