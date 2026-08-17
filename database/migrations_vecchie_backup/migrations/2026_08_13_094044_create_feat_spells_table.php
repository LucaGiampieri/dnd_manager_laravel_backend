<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_spells', function (Blueprint $table) {
            $table->id();

            //Talento che concede l'incantesimo
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Incantesimo concesso dal talento
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Modalità con cui viene concesso l'incantesimo
            $table->enum('grant_type', [
                'known',
                'always_known',
                'always_prepared',
                'granted',
            ])->default('granted');

            //Caratteristica usata per lanciare l'incantesimo
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Numero massimo di utilizzi gratuiti
            $table->unsignedTinyInteger('max_uses')
                ->nullable();

            //Tipo di recupero degli utilizzi gratuiti
            $table->enum('recharge', [
                'short_rest',
                'long_rest',
                'dawn',
                'other',
            ])->nullable();

            //Permette di usare anche gli slot incantesimo
            $table->boolean('can_use_spell_slots')
                ->default(true);

            //Livello a cui viene lanciato gratuitamente
            $table->unsignedTinyInteger('cast_at_level')
                ->nullable();

            //Indica se conta contro eventuali limiti di incantesimi
            $table->boolean('counts_against_limit')
                ->default(false);

            //Condizione necessaria per ottenere l'incantesimo
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso incantesimo
            //con la stessa modalità di concessione
            $table->unique([
                'feat_id',
                'spell_id',
                'grant_type',
            ], 'feat_spells_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_spells');
    }
};
