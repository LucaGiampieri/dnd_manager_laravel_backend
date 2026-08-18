<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('language_scripts', function (Blueprint $table) {
            $table->id();

            //Chiave tecnica stabile dell’alfabeto
            $table->string('key')
                ->unique();

            //Nome italiano dell’alfabeto
            $table->string('name')
                ->unique();

            //Descrizione dell’alfabeto
            $table->text('description')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            $table->timestamps();
        });

        Schema::table('languages', function (Blueprint $table) {
            //Chiave tecnica stabile della lingua
            $table->string('key')
                ->unique();

            //Categoria: standard, exotic, secret, dialect o creature
            $table->string('category')
                ->default('standard');

            //Lingua principale di cui questa lingua è un dialetto
            $table->foreignId('parent_language_id')
                ->nullable()
                ->constrained('languages')
                ->nullOnDelete();

            //Alfabeto normalmente utilizzato dalla lingua
            $table->foreignId('language_script_id')
                ->nullable()
                ->constrained('language_scripts')
                ->nullOnDelete();

            //Popoli o creature che utilizzano normalmente la lingua
            $table->text('typical_speakers')
                ->nullable();

            //Indica se è necessario il permesso del DM per sceglierla
            $table->boolean('requires_dm_permission')
                ->default(false);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropForeign(['parent_language_id']);
            $table->dropForeign(['language_script_id']);
            $table->dropUnique(['key']);

            $table->dropColumn([
                'key',
                'category',
                'parent_language_id',
                'language_script_id',
                'typical_speakers',
                'requires_dm_permission',
                'sort_order',
            ]);
        });

        Schema::dropIfExists('language_scripts');
    }
};
