<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_summon_monsters', function (Blueprint $table) {

            $table->id();

            //Opzione di evocazione dello spell
            $table->foreignId('spell_summon_id')
                ->constrained('spell_summons')
                ->cascadeOnDelete();

            //Mostro che può essere evocato
            $table->foreignId('monster_id')
                ->constrained('monsters')
                ->cascadeOnDelete();

            //Condizione necessaria per poter scegliere il mostro
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di collegare due volte lo stesso mostro
            //alla stessa opzione di evocazione
            $table->unique([
                'spell_summon_id',
                'monster_id',
            ], 'spell_summon_monsters_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_summon_monsters');
    }
};
