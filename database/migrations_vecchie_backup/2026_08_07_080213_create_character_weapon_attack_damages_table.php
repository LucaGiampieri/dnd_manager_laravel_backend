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
        Schema::create('character_weapon_attack_damages', function (Blueprint $table) {

            $table->id();

            //Attacco a cui appartiene questo componente di danno
            $table->foreignId('character_weapon_attack_id')
                ->constrained('character_weapon_attacks')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Dadi di danno
            $table->string('dice')
                ->nullable();

            //Bonus fisso specifico di questo componente di danno
            $table->integer('bonus')
                ->default(0);

            //Indica se a QUESTO componente deve essere aggiunto il modificatore della caratteristica definita in character_weapon_attacks.damage_ability_id
            $table->boolean('applies_ability_modifier')
                ->default(false);

            //Origine di questo danno aggiuntivo o personalizzato
            $table->enum('source_type', [
                'item',
                'class',
                'subclass',
                'race',
                'subrace',
                'feat',
                'feature',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuale condizione necessaria perché il danno venga applicato
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
        Schema::dropIfExists('character_weapon_attack_damages');
    }
};
