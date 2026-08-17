<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_features', function (Blueprint $table) {
            $table->id();

            //Talento che concede la feature
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Feature concessa dal talento
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Ordine di visualizzazione della feature
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di collegare due volte la stessa feature allo stesso talento
            $table->unique([
                'feat_id',
                'feature_id',
            ], 'feat_features_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_features');
    }
};
