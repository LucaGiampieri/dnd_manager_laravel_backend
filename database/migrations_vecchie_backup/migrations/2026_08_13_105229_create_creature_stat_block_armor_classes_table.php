<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_armor_classes', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene la CA
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Valore della Classe Armatura
            $table->unsignedSmallInteger('armor_class');

            //Origine o metodo di calcolo della CA
            $table->enum('armor_class_type', [
                'fixed',
                'natural_armor',
                'armor',
                'unarmored',
                'spell',
                'other',
            ])->default('fixed');

            //Indica la CA normalmente utilizzata
            $table->boolean('is_default')
                ->default(true);

            //Descrizione dell'origine della CA
            $table->string('description')
                ->nullable();

            //Condizione necessaria per utilizzare questa CA
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero delle CA dello stat block
            $table->index([
                'creature_stat_block_id',
                'is_default',
            ], 'creature_stat_block_armor_classes_default_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_armor_classes');
    }
};
