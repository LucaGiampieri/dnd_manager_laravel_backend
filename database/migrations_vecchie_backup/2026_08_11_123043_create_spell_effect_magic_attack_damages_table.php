<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_magic_attack_damages', function (Blueprint $table) {

            $table->id();

            //Attacco magico temporaneo a cui appartiene questo componente di danno
            $table->foreignId('spell_effect_magic_attack_id')
                ->constrained('spell_effect_magic_attacks')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Dadi di danno
            $table->string('dice')
                ->nullable();

            //Bonus fisso al danno
            $table->integer('bonus')
                ->default(0);

            //Se true, aggiunge al danno il modificatore della caratteristica da incantatore
            $table->boolean('applies_spellcasting_ability_modifier')
                ->default(false);

            //Indica il componente principale del danno dell'attacco
            $table->boolean('primary')
                ->default(true);

            //Eventuale comportamento specifico quando il bersaglio supera il TS
            $table->enum('save_success_damage_override', [
                'none',
                'half',
                'full'
            ])
                ->nullable();

            //Eventuale condizione per applicare questo componente di danno
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione/calcolo
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_magic_attack_damages');
    }
};
