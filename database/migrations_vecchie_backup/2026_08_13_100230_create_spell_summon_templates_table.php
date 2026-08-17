<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_summon_templates', function (Blueprint $table) {
            $table->id();

            //Evocazione dello spell a cui appartiene il template
            $table->foreignId('spell_summon_id')
                ->constrained('spell_summons')
                ->cascadeOnDelete();

            //Nome dello stat block evocato
            $table->string('name');

            //Tipo di creatura dello stat block
            $table->foreignId('creature_type_id')
                ->nullable()
                ->constrained('creature_types')
                ->nullOnDelete();

            //Taglia base dello stat block
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();

            //Descrizione generale dello stat block
            $table->text('description')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita template duplicati nella stessa evocazione
            $table->unique([
                'spell_summon_id',
                'name',
            ], 'spell_summon_templates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_summon_templates');
    }
};
