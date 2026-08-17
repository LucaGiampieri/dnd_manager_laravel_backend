<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_settings', function (Blueprint $table) {
            $table->id();

            //Campagna configurata
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            //Regolamento principale usato dalla campagna
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->restrictOnDelete();

            //Regole opzionali del Manuale del Giocatore
            $table->boolean('allow_multiclass')->default(false);
            $table->boolean('allow_feats')->default(false);

            //Altre opzioni comuni di campagna
            $table->boolean('allow_homebrew')->default(false);
            $table->boolean('use_encumbrance')->default(false);
            $table->boolean('use_milestone_leveling')->default(false);

            //Opzioni aggiuntive non ancora trasformate in colonne dedicate
            $table->json('optional_rules')->nullable();

            $table->timestamps();

            //Una sola configurazione per campagna
            $table->unique('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_settings');
    }
};
