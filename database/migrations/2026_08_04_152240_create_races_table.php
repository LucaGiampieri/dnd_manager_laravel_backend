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
        Schema::create('races', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene la razza o il lignaggio
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome della razza
            $table->string('name');

            //Descrizione generale della razza
            $table->text('description')
                ->nullable();

            //Allineamento tipico della razza
            $table->string('typical_alignment')
                ->nullable();

            //Eventuali note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'races_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'races_ruleset_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
