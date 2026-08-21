<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea il collegamento tra campagne e regole opzionali
    public function up(): void
    {
        Schema::create(
            'campaign_optional_rules',
            function (Blueprint $table) {
                $table->id();

                //Campagna che configura la regola opzionale
                $table->foreignId('campaign_id')
                    ->constrained('campaigns')
                    ->cascadeOnDelete();

                //Regola opzionale configurata dalla campagna
                $table->foreignId('optional_rule_id')
                    ->constrained('optional_rules')
                    ->cascadeOnDelete();

                //Indica se la regola è attiva nella campagna
                $table->boolean('enabled')
                    ->default(true);

                //Configurazione aggiuntiva specifica della campagna
                $table->json('configuration')
                    ->nullable();

                //Annotazioni specifiche della campagna
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Impedisce di configurare due volte la stessa regola
                //all'interno della stessa campagna
                $table->unique(
                    [
                        'campaign_id',
                        'optional_rule_id',
                    ],
                    'campaign_optional_rules_unique'
                );

                //Velocizza il recupero delle regole attive
                $table->index(
                    [
                        'campaign_id',
                        'enabled',
                    ],
                    'campaign_optional_rules_enabled_index'
                );
            }
        );
    }

    //Elimina il collegamento tra campagne e regole opzionali
    public function down(): void
    {
        Schema::dropIfExists('campaign_optional_rules');
    }
};
