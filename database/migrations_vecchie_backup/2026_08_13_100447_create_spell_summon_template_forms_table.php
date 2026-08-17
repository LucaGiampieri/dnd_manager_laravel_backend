<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_summon_template_forms', function (Blueprint $table) {
            $table->id();

            //Template di evocazione a cui appartiene la forma
            $table->foreignId('spell_summon_template_id')
                ->constrained('spell_summon_templates')
                ->cascadeOnDelete();

            //Nome della forma
            $table->string('name');

            //Descrizione della forma
            $table->text('description')
                ->nullable();

            //Indica se questa è la forma predefinita
            $table->boolean('is_default')
                ->default(false);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita forme duplicate nello stesso template
            $table->unique([
                'spell_summon_template_id',
                'name',
            ], 'spell_summon_template_forms_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_summon_template_forms');
    }
};
