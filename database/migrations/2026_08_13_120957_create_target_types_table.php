<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('target_types', function (Blueprint $table) {
            $table->id();

            //Chiave tecnica del tipo di bersaglio
            $table->string('key')
                ->unique();

            //Nome del tipo di bersaglio
            $table->string('name')
                ->unique();

            //Descrizione del tipo di bersaglio
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
        Schema::dropIfExists('target_types');
    }
};
