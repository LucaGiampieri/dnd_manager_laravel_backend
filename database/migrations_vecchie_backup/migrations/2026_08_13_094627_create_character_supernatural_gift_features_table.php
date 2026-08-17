<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_supernatural_gift_features', function (Blueprint $table) {
            $table->id();

            //Acquisizione del dono soprannaturale
            $table->foreignId('character_supernatural_gift_id')
                ->constrained('character_supernatural_gifts')
                ->cascadeOnDelete();

            //Feature prevista dal dono soprannaturale
            $table->foreignId('supernatural_gift_feature_id')
                ->constrained('supernatural_gift_features')
                ->cascadeOnDelete();

            //Feature effettivamente posseduta dal personaggio
            $table->foreignId('character_feature_id')
                ->constrained('character_feature')
                ->cascadeOnDelete();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di generare due volte la stessa feature dalla stessa acquisizione del dono
            $table->unique([
                'character_supernatural_gift_id',
                'supernatural_gift_feature_id',
            ], 'character_supernatural_gift_features_unique');

            //Una specifica character_feature deriva da una sola acquisizione di un dono soprannaturale
            $table->unique('character_feature_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_supernatural_gift_features');
    }
};
