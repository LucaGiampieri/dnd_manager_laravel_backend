<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        //Modifica la quantità posseduta dal personaggio
        //perché le singole monete vengono registrate come numeri interi
        Schema::table(
            'character_currencies',
            function (Blueprint $table) {
                //Utilizza un intero positivo di grandi dimensioni
                //e impedisce quantità negative
                $table->unsignedBigInteger('amount')
                    ->default(0)
                    ->change();
            }
        );
    }

    public function down(): void
    {
        //Ripristina il precedente tipo decimale
        //quando la migrazione viene annullata
        Schema::table(
            'character_currencies',
            function (Blueprint $table) {
                $table->decimal('amount', 12, 2)
                    ->default(0)
                    ->change();
            }
        );
    }
};
