<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_feat_features', function (Blueprint $table) {
            $table->id();

            //Acquisizione del talento da cui deriva la feature
            $table->foreignId('character_feat_id')
                ->constrained('character_feats')
                ->cascadeOnDelete();

            //Feature prevista dal talento
            $table->foreignId('feat_feature_id')
                ->constrained('feat_features')
                ->cascadeOnDelete();

            //Feature effettivamente posseduta dal personaggio
            $table->foreignId('character_feature_id')
                ->constrained('character_feature')
                ->cascadeOnDelete();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di generare due volte la stessa feature
            //dalla stessa acquisizione del talento
            $table->unique([
                'character_feat_id',
                'feat_feature_id',
            ], 'character_feat_features_unique');

            //Una specifica character_feature appartiene
            //a una sola acquisizione del talento
            $table->unique('character_feature_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feat_features');
    }
};
