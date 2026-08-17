<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('supernatural_gifts', function (Blueprint $table) {
            $table->id();

            //Tipo di dono soprannaturale
            $table->foreignId('supernatural_gift_type_id')
                ->constrained('supernatural_gift_types')
                ->cascadeOnDelete();

            //Nome del dono soprannaturale
            $table->string('name');

            //Descrizione del dono soprannaturale
            $table->text('description');

            //Indica se il dono può essere ottenuto più volte
            $table->boolean('repeatable')
                ->default(false);

            //Numero massimo di volte che può essere ottenuto
            $table->unsignedTinyInteger('max_times')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita doni duplicati dello stesso tipo
            $table->unique([
                'supernatural_gift_type_id',
                'name',
            ], 'supernatural_gifts_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supernatural_gifts');
    }
};
