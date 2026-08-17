<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_feats', function (Blueprint $table) {
            $table->id();

            //Personaggio che possiede il talento
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Talento posseduto
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Fonte da cui è stato ottenuto il talento
            $table->enum('source_type', [
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feature',
                'manual',
                'other',
            ])->default('manual');

            //ID della fonte che ha concesso il talento
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Livello del personaggio in cui è stato ottenuto
            $table->unsignedTinyInteger('acquired_at_level')
                ->nullable();

            //Numero dell'acquisizione dello stesso talento
            $table->unsignedTinyInteger('instance_number')
                ->default(1);

            //Indica se il talento è attualmente attivo
            $table->boolean('active')
                ->default(true);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa acquisizione
            $table->unique([
                'character_id',
                'feat_id',
                'instance_number',
            ], 'character_feats_unique');

            //Velocizza la ricerca in base alla provenienza
            $table->index([
                'source_type',
                'source_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feats');
    }
};
