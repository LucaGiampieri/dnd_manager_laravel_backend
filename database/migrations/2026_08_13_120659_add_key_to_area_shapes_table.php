<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('area_shapes', function (Blueprint $table) {
            //Chiave tecnica stabile della forma
            $table->string('key')
                ->unique()
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('area_shapes', function (Blueprint $table) {
            $table->dropUnique('area_shapes_key_unique');
            $table->dropColumn('key');
        });
    }
};
