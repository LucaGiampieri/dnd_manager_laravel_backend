<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subrace_features', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente la capacità
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Capacità concessa dalla sottorazza
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Livello del personaggio al quale la capacità viene ottenuta
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita duplicati
            $table->unique([
                'subrace_id',
                'feature_id',
                'level'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subrace_features');
    }
};
