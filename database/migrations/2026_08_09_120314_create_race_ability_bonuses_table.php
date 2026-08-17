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
        Schema::create('race_ability_bonuses', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente il bonus
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Caratteristica che riceve il bonus
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Valore del bonus
            $table->integer('bonus')
                ->default(1);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una razza non può assegnare due volte il bonus automatico alla stessa caratteristica
            $table->unique([
                'race_id',
                'ability_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_ability_bonuses');
    }
};
