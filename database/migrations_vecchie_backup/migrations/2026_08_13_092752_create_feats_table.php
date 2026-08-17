<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feats', function (Blueprint $table) {
            $table->id();

            //Nome del talento
            $table->string('name');

            //Descrizione del talento
            $table->text('description');

            //Indica se il talento può essere scelto più volte
            $table->boolean('repeatable')
                ->default(false);

            //Numero massimo di volte che può essere scelto
            $table->unsignedTinyInteger('max_times')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita talenti duplicati con lo stesso nome
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feats');
    }
};
