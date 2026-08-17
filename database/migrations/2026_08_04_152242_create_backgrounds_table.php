<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backgrounds', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene il background
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome del background
            $table->string('name');

            //Descrizione generale del background
            $table->text('description')
                ->nullable();

            //Descrizione narrativa/origine
            $table->text('origin_description')
                ->nullable();

            //Eventuali suggerimenti per personalità, ideali, legami e difetti
            $table->text('roleplay_suggestions')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'backgrounds_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'backgrounds_ruleset_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backgrounds');
    }
};
