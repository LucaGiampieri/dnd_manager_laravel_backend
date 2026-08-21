<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        //Aggiunge i campi necessari per distinguere
        //versioni differenti dello stesso oggetto
        Schema::table('items', function (Blueprint $table) {
            //Chiave condivisa dalle diverse versioni dell'oggetto
            $table->string('canonical_key')
                ->after('key');

            //Fonte o versione meccanica dell'oggetto
            $table->string('version_key')
                ->after('canonical_key');

            //Indica se questa versione è stata superata
            $table->boolean('is_legacy')
                ->default(false)
                ->after('version_key');

            //Permette una sola riga per ogni versione canonica
            $table->unique([
                'ruleset_id',
                'canonical_key',
                'version_key',
            ], 'items_ruleset_canonical_version_unique');

            //Velocizza il recupero delle versioni dello stesso oggetto
            $table->index([
                'ruleset_id',
                'canonical_key',
            ], 'items_ruleset_canonical_index');
        });

        //Permette di rappresentare danni fissi privi di dado
        Schema::table(
            'item_weapon_damages',
            function (Blueprint $table) {
                $table->unsignedSmallInteger('dice_count')
                    ->nullable()
                    ->change();

                $table->unsignedSmallInteger('die_size')
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        //Converte temporaneamente i danni fissi
        //in valori numerici compatibili con il vecchio schema
        DB::table('item_weapon_damages')
            ->whereNull('dice_count')
            ->update([
                'dice_count' => 0,
                'die_size' => 0,
            ]);

        //Ripristina l'obbligatorietà dei campi relativi ai dadi
        Schema::table(
            'item_weapon_damages',
            function (Blueprint $table) {
                $table->unsignedSmallInteger('dice_count')
                    ->nullable(false)
                    ->change();

                $table->unsignedSmallInteger('die_size')
                    ->nullable(false)
                    ->change();
            }
        );

        //Rimuove i campi di versionamento
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique(
                'items_ruleset_canonical_version_unique'
            );

            $table->dropIndex(
                'items_ruleset_canonical_index'
            );

            $table->dropColumn([
                'canonical_key',
                'version_key',
                'is_legacy',
            ]);
        });
    }
};
