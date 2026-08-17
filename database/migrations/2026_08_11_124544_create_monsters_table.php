<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('monsters', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene la creatura
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome della creatura
            $table->string('name');

            //Descrizione generale
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'monsters_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'monsters_ruleset_name_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monsters');
    }
};
