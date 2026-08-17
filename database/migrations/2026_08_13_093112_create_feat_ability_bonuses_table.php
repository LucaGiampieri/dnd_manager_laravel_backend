<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_ability_bonuses', function (Blueprint $table) {
            $table->id();

            //Talento che concede il bonus
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Caratteristica aumentata
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Bonus applicato alla caratteristica
            $table->integer('bonus')
                ->default(1);

            //Valore massimo raggiungibile con questo bonus
            $table->unsignedTinyInteger('max_score')
                ->nullable();

            //Condizione necessaria per ottenere il bonus
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso bonus
            $table->unique([
                'feat_id',
                'ability_id',
            ], 'feat_ability_bonuses_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_ability_bonuses');
    }
};
