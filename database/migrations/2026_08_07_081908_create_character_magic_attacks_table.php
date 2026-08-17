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
        Schema::create('character_magic_attacks', function (Blueprint $table) {

            $table->id();

            //Personaggio/NPC che possiede l'attacco
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome visualizzato
            $table->string('name');

            //Origine dell'attacco
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Come viene risolto l'attacco
            //Attack_roll: richiede un tiro per colpire
            //Saving_throw: il bersaglio effettua un TS
            //Automatic: non richiede né tiro per colpire né TS
            $table->enum('resolution_type', [
                'attack_roll',
                'saving_throw',
                'automatic'
            ]);

            //Tipo di tiro per colpire
            $table->enum('attack_type', [
                'melee',
                'ranged'
            ])
                ->nullable();

            //Caratteristica usata nel tiro per colpire
            $table->foreignId('attack_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Indica se normalmente si aggiunge il bonus di competenza al tiro per colpire
            $table->boolean('attack_uses_proficiency')
                ->default(true);

            //Bonus aggiuntivo al tiro per colpire
            $table->integer('attack_bonus')
                ->default(0);

            //Se presente, sostituisce completamente il bonus per colpire calcolato
            $table->integer('attack_bonus_override')
                ->nullable();

            //Caratteristica normalmente usata per eventuali modificatori al danno
            $table->foreignId('damage_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Caratteristica del tiro salvezza richiesto al bersaglio
            $table->foreignId('saving_throw_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Caratteristica usata per calcolare normalmente la CD
            $table->foreignId('save_dc_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Se il bonus di competenza entra nel calcolo della CD
            $table->boolean('save_uses_proficiency')
                ->default(true);

            //Bonus aggiuntivo alla CD
            $table->integer('save_dc_bonus')
                ->default(0);

            //Se valorizzata, sostituisce completamentela CD calcolata
            $table->unsignedSmallInteger('save_dc_override')
                ->nullable();

            //Cosa succede al danno se il bersaglio supera il tiro salvezza
            $table->enum('save_success_damage', [
                'none',
                'half',
                'full'
            ])
                ->nullable();

            //Portata in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Indica se l'attacco è attualmente disponibile/visualizzato
            $table->boolean('active')
                ->default(true);

            //Eventuali note o regole particolari
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
        Schema::dropIfExists('character_magic_attacks');
    }
};
