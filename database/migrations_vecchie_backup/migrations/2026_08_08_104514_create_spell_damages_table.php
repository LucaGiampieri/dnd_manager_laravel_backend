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
        Schema::create('spell_damages', function (Blueprint $table) {

            $table->id();

            //Incantesimo a cui appartiene questo componente di danno
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Dadi di danno base
            $table->string('dice')
                ->nullable();

            //Eventuale bonus fisso
            $table->integer('bonus')
                ->default(0);

            //Indica se al danno deve essere aggiunto il modificatore della caratteristica da incantatore
            $table->boolean('applies_ability_modifier')
                ->default(false);

            //Indica il componente principale del danno dello spell
            $table->boolean('primary')
                ->default(true);

            //Eventuale comportamento specifico quando il bersaglio supera il TS
            $table->enum('save_success_damage_override', [
                'none',
                'half',
                'full'
            ])
                ->nullable();

            //Eventuale condizione necessaria per applicare questo componente
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spell_damages');
    }
};
