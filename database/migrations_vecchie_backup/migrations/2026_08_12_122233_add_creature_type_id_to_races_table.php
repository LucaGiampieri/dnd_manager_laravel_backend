<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {

            //Tipo di creatura della razza
            $table->foreignId('creature_type_id')
                ->nullable()
                ->after('name')
                ->constrained('creature_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {

            //Rimuove il collegamento con il tipo di creatura
            $table->dropConstrainedForeignId('creature_type_id');
        });
    }
};
