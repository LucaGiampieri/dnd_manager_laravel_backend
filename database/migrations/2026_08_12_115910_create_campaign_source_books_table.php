<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_source_books', function (Blueprint $table) {
            $table->id();

            //Campagna che abilita la fonte
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            //Manuale disponibile nella campagna
            $table->foreignId('source_book_id')
                ->constrained('source_books')
                ->cascadeOnDelete();

            //Permette di disattivare temporaneamente la fonte
            $table->boolean('enabled')->default(true);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique([
                'campaign_id',
                'source_book_id',
            ], 'campaign_source_books_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_source_books');
    }
};
