<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        //Rinomina la colonna per specificare chiaramente
        //che rappresenta il lato in metri dello spazio controllato
        Schema::table('sizes', function (Blueprint $table) {
            $table->renameColumn(
                'space',
                'space_side_meters'
            );
        });
    }

    public function down(): void
    {
        //Ripristina il precedente nome della colonna
        //quando la migrazione viene annullata
        Schema::table('sizes', function (Blueprint $table) {
            $table->renameColumn(
                'space_side_meters',
                'space'
            );
        });
    }
};
