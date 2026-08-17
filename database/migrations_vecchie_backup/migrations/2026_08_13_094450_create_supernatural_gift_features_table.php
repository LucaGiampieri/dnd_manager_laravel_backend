<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('supernatural_gift_features', function (Blueprint $table) {
            $table->id();

            //Dono soprannaturale che concede la feature
            $table->foreignId('supernatural_gift_id')
                ->constrained('supernatural_gifts')
                ->cascadeOnDelete();

            //Feature concessa dal dono soprannaturale
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

            //Evita di collegare due volte la stessa feature allo stesso dono
            $table->unique([
                'supernatural_gift_id',
                'feature_id',
            ], 'supernatural_gift_features_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supernatural_gift_features');
    }
};
