<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('creature_types', function (Blueprint $table) {
            //Chiave tecnica stabile del tipo di creatura
            $table->string('key')
                ->unique();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('creature_types', function (Blueprint $table) {
            $table->dropUnique(['key']);

            $table->dropColumn([
                'key',
                'sort_order',
            ]);
        });
    }
};
