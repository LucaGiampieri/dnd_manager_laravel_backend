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
        Schema::create('character_magic_attack_damages', function (Blueprint $table) {

            $table->id();

            //Attacco magico a cui appartiene questo componente di danno
            $table->foreignId('character_magic_attack_id')
                ->constrained('character_magic_attacks')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Numero di dadi del danno
            $table->unsignedSmallInteger('dice_count')
                ->nullable();

            //Numero di facce del dado
            $table->unsignedSmallInteger('die_size')
                ->nullable();

            //Bonus fisso specifico di questo componente
            $table->integer('bonus')
                ->default(0);

            //Indica se aggiungere il modificatore della caratteristica definita in character_magic_attacks.damage_ability_id
            $table->boolean('applies_ability_modifier')
                ->default(false);

            //Indica se questo è il componente principale del danno
            $table->boolean('primary')
                ->default(true);

            //Eventuale condizione necessaria affinché questo danno venga applicato
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_magic_attack_damages');
    }
};
