<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge tempi espressi in azioni ai profili di armature e scudi
    public function up(): void
    {
        Schema::table('item_armor_profiles', function (Blueprint $table) {
            //Numero di azioni necessarie per indossare l'oggetto
            $table->unsignedSmallInteger('don_time_actions')
                ->nullable()
                ->after('don_time_minutes');

            //Numero di azioni necessarie per rimuovere l'oggetto
            $table->unsignedSmallInteger('doff_time_actions')
                ->nullable()
                ->after('doff_time_minutes');
        });
    }

    //Rimuove i tempi espressi in azioni
    public function down(): void
    {
        Schema::table('item_armor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'don_time_actions',
                'doff_time_actions',
            ]);
        });
    }
};
