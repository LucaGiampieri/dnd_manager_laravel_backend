<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_ability_bonuses', function (Blueprint $table) {

            $table->id();

            //Background che concede automaticamente il bonus
            $table->foreignId('background_id')
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

            //Lo stesso background non può assegnare due volte un bonus alla stessa caratteristica
            $table->unique([
                'background_id',
                'ability_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_ability_bonuses');
    }
};
