<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('supernatural_gift_types', function (Blueprint $table) {
            $table->id();

            //Nome del tipo di dono soprannaturale
            $table->string('name')
                ->unique();

            //Descrizione generale del tipo
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
        Schema::dropIfExists('supernatural_gift_types');
    }
};
