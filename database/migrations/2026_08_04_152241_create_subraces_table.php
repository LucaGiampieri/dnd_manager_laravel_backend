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
        Schema::create('subraces', function (Blueprint $table) {

            $table->id();

            //Razza principale a cui appartiene la sottorazza
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Nome della sottorazza
            $table->string('name');

            //Descrizione generale
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una razza non può avere due sottorazze con lo stesso nome
            $table->unique([
                'race_id',
                'name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subraces');
    }
};
