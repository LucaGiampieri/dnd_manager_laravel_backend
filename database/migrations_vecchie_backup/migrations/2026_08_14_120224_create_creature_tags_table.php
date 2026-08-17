<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_tags', function (Blueprint $table) {
            $table->id();

            //Chiave tecnica del tag
            $table->string('key')
                ->unique();

            //Nome del tag
            $table->string('name')
                ->unique();

            //Descrizione del tag
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_tags');
    }
};
