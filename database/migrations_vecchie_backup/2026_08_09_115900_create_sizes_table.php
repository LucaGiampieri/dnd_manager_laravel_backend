<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {

            $table->id();

            //Nome della categoria di taglia
            $table->string('name')
                ->unique();

            //Posizione della taglia nella scala
            $table->smallInteger('sort_order')
                ->unique();

            //Spazio normalmente occupato dalla creatura, espresso in metri
            $table->float('space')
                ->nullable();

            //Descrizione o regole aggiuntive della taglia
            $table->text('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
