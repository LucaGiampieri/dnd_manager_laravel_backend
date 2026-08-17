<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_spellcasting_spells', function (Blueprint $table) {
            $table->id();

            //Profilo da incantatore a cui appartiene l'incantesimo
            $table->foreignId('creature_stat_block_spellcasting_profile_id')
                ->constrained(table: 'creature_stat_block_spellcasting_profiles', indexName: 'fk_creature_stat_block_spellcasting_spells_creature_sta_afe2627a')
                ->cascadeOnDelete();

            //Incantesimo conosciuto o utilizzabile
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Modalità con cui l'incantesimo può essere lanciato
            $table->enum('casting_type', [
                'slot',
                'at_will',
                'per_day',
                'recharge',
                'other',
            ])->default('slot');

            //Numero massimo di utilizzi
            $table->unsignedTinyInteger('max_uses')
                ->nullable();

            //Indica se gli utilizzi sono condivisi con altri incantesimi
            $table->boolean('shared_uses')
                ->default(false);

            //Chiave del gruppo di utilizzi condivisi
            $table->string('shared_uses_key')
                ->nullable();

            //Livello a cui viene normalmente lanciato
            $table->unsignedTinyInteger('cast_at_level')
                ->nullable();

            //Valore minimo del tiro di ricarica
            $table->unsignedTinyInteger('recharge_min')
                ->nullable();

            //Valore massimo del tiro di ricarica
            $table->unsignedTinyInteger('recharge_max')
                ->nullable();

            //Indica se richiede concentrazione normalmente
            $table->boolean('requires_concentration')
                ->nullable();

            //Condizione particolare per utilizzare l'incantesimo
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero degli incantesimi del profilo
            $table->index([
                'creature_stat_block_spellcasting_profile_id',
                'casting_type',
                'sort_order',
            ], 'creature_spellcasting_spells_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_spellcasting_spells');
    }
};
