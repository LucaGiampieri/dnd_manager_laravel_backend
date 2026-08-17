<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_feature_replacements', function (Blueprint $table) {
            $table->id();

            //Feature di classe che effettua la sostituzione
            $table->foreignId('class_feature_id')
                ->constrained('class_features')
                ->cascadeOnDelete();

            //Feature di classe che viene sostituita
            $table->foreignId('replaced_class_feature_id')
                ->constrained('class_features')
                ->cascadeOnDelete();

            //Condizione necessaria per effettuare la sostituzione
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa sostituzione
            $table->unique([
                'class_feature_id',
                'replaced_class_feature_id',
            ], 'class_feature_replacements_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_feature_replacements');
    }
};
