<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tool_proficiency_items', function (Blueprint $table) {

            $table->id();

            //Proficienza nello strumento
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

            //Oggetto concreto appartenente a questa proficienza
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di collegare due volte lo stesso item alla stessa proficienza
            $table->unique([
                'tool_proficiency_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_proficiency_items');
    }
};
