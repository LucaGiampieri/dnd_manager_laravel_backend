<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {

            //Indica se la razza è un lignaggio
            $table->boolean('is_lineage')
                ->default(false)
                ->after('creature_type_id');

            //Indica se può sostituire una razza già posseduta
            $table->boolean('can_replace_race')
                ->default(false)
                ->after('is_lineage');
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {

            //Rimuove i campi relativi ai lineaggi
            $table->dropColumn([
                'is_lineage',
                'can_replace_race',
            ]);
        });
    }
};
