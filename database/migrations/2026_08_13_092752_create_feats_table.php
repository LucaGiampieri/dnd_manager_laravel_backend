<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feats', function (Blueprint $table) {
            $table->id();

            //Regolamento al quale appartiene il talento
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome del talento
            $table->string('name');

            //Descrizione del talento
            $table->text('description');

            //Indica se il talento può essere scelto più volte
            $table->boolean('repeatable')
                ->default(false);

            //Numero massimo di volte che può essere scelto
            $table->unsignedTinyInteger('max_times')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso regolamento
            $table->unique([
                'ruleset_id',
                'key',
            ], 'feats_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'feats_ruleset_name_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feats');
    }
};
