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
                ->constrained(table: 'character_supernatural_gifts', indexName: 'fk_character_supernatural_gift_features_character_super_db4e0a43')
                ->cascadeOnDelete();

            //Feature prevista dal dono soprannaturale
            $table->foreignId('supernatural_gift_feature_id')
                ->constrained(table: 'supernatural_gift_features', indexName: 'fk_character_supernatural_gift_features_supernatural_gi_9ede1e01')
                ->cascadeOnDelete();

            //Feature effettivamente posseduta dal personaggio
            $table->foreignId('character_feature_id')
                ->constrained(table: 'character_feature', indexName: 'fk_character_supernatural_gift_features_character_featu_25e1accb')
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
