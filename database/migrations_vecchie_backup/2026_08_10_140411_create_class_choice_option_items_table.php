<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_choice_option_items', function (Blueprint $table) {

            $table->id();

            //Opzione di classe a cui appartiene l'oggetto
            $table->foreignId('class_choice_option_id')
                ->constrained('class_choice_options')
                ->cascadeOnDelete();

            //Oggetto contenuto nell'opzione
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Quantità dell'oggetto
            $table->unsignedInteger('quantity')
                ->default(1);

            //Ordine dell'oggetto all'interno del pacchetto
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto non deve comparire due volte nella stessa opzione
            $table->unique([
                'class_choice_option_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_choice_option_items');
    }
};
