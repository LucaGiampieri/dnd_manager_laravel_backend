<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_equipment', function (Blueprint $table) {

            $table->id();

            //Classe che concede automaticamente l'oggetto
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            //Oggetto concesso dalla classe
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Livello della classe al quale viene concesso l'oggetto
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Quantità concessa
            $table->unsignedInteger('quantity')
                ->default(1);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto non viene inserito due volte per la stessa classe allo stesso livello
            $table->unique([
                'class_id',
                'item_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_equipment');
    }
};
