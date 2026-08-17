<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('class_features', function (Blueprint $table) {

            //Indica se la capacità di classe è opzionale
            $table->boolean('is_optional')
                ->default(false)
                ->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('class_features', function (Blueprint $table) {

            //Rimuove il campo delle capacità opzionali
            $table->dropColumn('is_optional');
        });
    }
};
