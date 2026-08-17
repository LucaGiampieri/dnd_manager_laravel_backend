<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('level_progressions', function (Blueprint $table) {

            $table->id();

            //Livello totale del personaggio
            $table->unsignedTinyInteger('level')
                ->unique();

            //Bonus di competenza previsto per questo livello totale
            $table->unsignedTinyInteger('proficiency_bonus');

            //Quantità totale di punti esperienza necessaria per raggiungere questo livello
            $table->unsignedInteger('experience_required')
                ->nullable();

            //Eventuali note o regole particolari
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_progressions');
    }
};
