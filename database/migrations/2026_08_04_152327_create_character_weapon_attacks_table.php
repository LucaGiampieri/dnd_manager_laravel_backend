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
        Schema::create('character_weapon_attacks', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede/utilizza questo attacco
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Istanza concreta dell'arma nell'inventario
            $table->foreignId('character_inventory_instance_id')
                ->nullable()
                ->constrained(
                    table: 'character_inventory_instances',
                    indexName: 'fk_character_weapon_attacks_inventory_instance'
                )
                ->nullOnDelete();

            //Nome visualizzato dell'attacco
            $table->string('name');

            //Caratteristica usata per il tiro per colpire
            $table->foreignId('attack_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Caratteristica utilizzata normalmente anche per il danno
            $table->foreignId('damage_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Se valorizzato, sostituisce il normale bonus per colpire calcolato dal sistema
            $table->integer('attack_bonus_override')
                ->nullable();

            //Bonus extra al tiro per colpire
            $table->integer('attack_bonus')
                ->default(0);

            //Se valorizzato, sostituisce il normale modificatore di caratteristica al danno
            $table->integer('damage_bonus_override')
                ->nullable();

            //Bonus extra fisso al danno
            $table->integer('damage_bonus')
                ->default(0);

            //Permette di sovrascrivere il tipo dell'attacco definito dall'item
            $table->enum('attack_type_override', [
                'melee',
                'ranged'
            ])
                ->nullable();

            //Portata personalizzata
            $table->decimal('range_override', 10, 3)
                ->nullable();

            //Portata lunga personalizzata
            $table->decimal('long_range_override', 10, 3)
                ->nullable();

            //Attacco disponibile nella scheda
            $table->boolean('active')
                ->default(true);

            //True = l'attacco utilizza automaticamente i danni definiti in item_weapon_damages.
            //False = ignora il danno base dell'item e usa soltanto i danni specificati per questo attacco
            $table->boolean('use_item_damage')
                ->default(true);

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
        Schema::dropIfExists('character_weapon_attacks');
    }
};
