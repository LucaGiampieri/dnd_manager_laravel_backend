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
        Schema::create('senses', function (Blueprint $table) {

            $table->id();

            //Nome del senso
            $table->enum('name', [
                'darkvision',
                'blindsight',
                'tremorsense',
                'truesight'
            ])
            ->unique();

            //Descrizione del senso
            $table->text('description')
            ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senses');
    }
};
