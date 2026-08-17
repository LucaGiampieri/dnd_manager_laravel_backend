<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene la classe
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            // Nome della classe
            $table->string('name');

            //Descrizione generale della classe
            $table->text('description')
                ->nullable();

            // Dado vita della classe
            $table->unsignedSmallInteger('hit_die_size');

            //Eventuali note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'classes_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'classes_ruleset_name_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
