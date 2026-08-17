<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_notes', function (Blueprint $table) {
            $table->id();

            //Personaggio al quale appartiene la nota
            $table->foreignId('character_id')
                ->constrained('characters')
                ->cascadeOnDelete();

            //Utente che ha scritto la nota
            $table->foreignId('author_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            //Titolo breve della nota
            $table->string('title')->nullable();

            //Contenuto della nota
            $table->longText('content');

            //Visibilità della nota nella campagna
            $table->enum('visibility', [
                'private',
                'master',
                'campaign',
            ])->default('private');

            //Permette di mettere in evidenza la nota
            $table->boolean('pinned')->default(false);

            //Ordine manuale facoltativo
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index([
                'character_id',
                'visibility',
                'pinned',
            ], 'character_notes_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_notes');
    }
};
