<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_supernatural_gifts', function (Blueprint $table) {
            $table->id();

            //Personaggio che possiede il dono soprannaturale
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Dono soprannaturale posseduto
            $table->foreignId('supernatural_gift_id')
                ->constrained('supernatural_gifts')
                ->cascadeOnDelete();

            //Fonte da cui è stato ottenuto il dono
            $table->enum('source_type', [
                'campaign',
                'feature',
                'item',
                'manual',
                'other',
            ])->default('manual');

            //ID della fonte che ha concesso il dono
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Livello del personaggio in cui è stato ottenuto
            $table->unsignedTinyInteger('acquired_at_level')
                ->nullable();

            //Numero dell'acquisizione dello stesso dono
            $table->unsignedTinyInteger('instance_number')
                ->default(1);

            //Indica se il dono è attualmente attivo
            $table->boolean('active')
                ->default(true);

            //Data in cui il dono è stato ottenuto
            $table->timestamp('acquired_at')
                ->nullable();

            //Data in cui il dono è stato perso o rimosso
            $table->timestamp('removed_at')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa acquisizione
            $table->unique([
                'character_id',
                'supernatural_gift_id',
                'instance_number',
            ], 'character_supernatural_gifts_unique');

            //Velocizza la ricerca in base alla provenienza
            $table->index([
                'source_type',
                'source_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_supernatural_gifts');
    }
};
