<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_blocks', function (Blueprint $table) {
            $table->id();

            //Mostro a cui appartiene lo stat block
            $table->foreignId('monster_id')
                ->nullable()
                ->constrained('monsters')
                ->cascadeOnDelete();

            //Nome dello stat block
            $table->string('name');

            //Tipo di creatura
            $table->foreignId('creature_type_id')
                ->nullable()
                ->constrained('creature_types')
                ->nullOnDelete();

            //Taglia della creatura
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();

            //Allineamento della creatura
            $table->string('alignment')
                ->nullable();

            //Grado di sfida della creatura
            $table->decimal('challenge_rating', 6, 3)
                ->nullable();

            //Bonus di competenza dello stat block
            $table->smallInteger('proficiency_bonus')
                ->nullable();

            //Descrizione generale dello stat block
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza la ricerca degli stat block di un mostro
            $table->index([
                'monster_id',
                'name',
            ], 'creature_stat_blocks_monster_name_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_blocks');
    }
};
