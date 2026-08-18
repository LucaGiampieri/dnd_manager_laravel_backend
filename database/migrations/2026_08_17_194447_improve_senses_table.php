<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('senses', function (Blueprint $table) {
            $table->dropUnique('senses_name_unique');
        });

        Schema::table('senses', function (Blueprint $table) {
            $table->renameColumn('name', 'key');
        });

        Schema::table('senses', function (Blueprint $table) {
            // Da enum chiuso a stringa estendibile
            $table->string('key')->change();

            $table->unique(
                'key',
                'senses_key_unique'
            );

            // Nome italiano mostrato all’utente
            $table->string('name');

            $table->unique(
                'name',
                'senses_display_name_unique'
            );

            $table->unsignedSmallInteger('sort_order')
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('senses', function (Blueprint $table) {
            $table->dropUnique('senses_key_unique');
            $table->dropUnique('senses_display_name_unique');

            $table->dropColumn([
                'name',
                'sort_order',
            ]);
        });

        Schema::table('senses', function (Blueprint $table) {
            $table->enum('key', [
                'darkvision',
                'blindsight',
                'tremorsense',
                'truesight',
            ])->change();
        });

        Schema::table('senses', function (Blueprint $table) {
            $table->renameColumn('key', 'name');
        });

        Schema::table('senses', function (Blueprint $table) {
            $table->unique(
                'name',
                'senses_name_unique'
            );
        });
    }
};
