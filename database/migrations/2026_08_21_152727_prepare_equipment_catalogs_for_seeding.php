<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        //Completa il catalogo delle tipologie di oggetto
        Schema::table('item_types', function (Blueprint $table) {
            //Chiave tecnica stabile utilizzata dai seeder
            $table->string('key')
                ->after('id');

            //Ordine di visualizzazione nel catalogo
            $table->unsignedSmallInteger('sort_order')
                ->default(0)
                ->after('description');

            //Evita tipologie duplicate
            $table->unique(
                'key',
                'item_types_key_unique'
            );

            $table->unique(
                'name',
                'item_types_name_unique'
            );
        });

        //Aggiunge l'ordinamento agli oggetti
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedSmallInteger('sort_order')
                ->default(0)
                ->after('notes');

            //Velocizza il catalogo degli oggetti per regolamento e tipologia
            $table->index([
                'ruleset_id',
                'item_type_id',
                'sort_order',
            ], 'items_ruleset_type_sort_index');
        });

        //Distingue le categorie meccaniche delle armi
        Schema::table(
            'item_weapon_profiles',
            function (Blueprint $table) {
                $table->enum('weapon_category', [
                    'simple',
                    'martial',
                    'improvised',
                    'natural',
                    'firearm',
                    'siege',
                    'other',
                ])
                    ->default('other')
                    ->after('item_id');

                $table->index(
                    'weapon_category',
                    'item_weapon_profiles_category_index'
                );
            }
        );

        //Distingue le categorie meccaniche delle armature
        Schema::table(
            'item_armor_profiles',
            function (Blueprint $table) {
                $table->enum('armor_category', [
                    'light',
                    'medium',
                    'heavy',
                    'shield',
                    'natural',
                    'other',
                ])
                    ->default('other')
                    ->after('item_id');

                $table->index(
                    'armor_category',
                    'item_armor_profiles_category_index'
                );
            }
        );

        //Rende le proprietà delle armi identificabili e versionabili
        Schema::table(
            'weapon_properties',
            function (Blueprint $table) {
                //Rimuove il vecchio vincolo globale sul nome
                $table->dropUnique(
                    'weapon_properties_name_unique'
                );

                //Regolamento a cui appartiene la proprietà
                $table->foreignId('ruleset_id')
                    ->after('id')
                    ->constrained('rulesets')
                    ->cascadeOnDelete();

                //Chiave tecnica stabile
                $table->string('key')
                    ->after('ruleset_id');

                //Ordine ufficiale di visualizzazione
                $table->unsignedSmallInteger('sort_order')
                    ->default(0)
                    ->after('notes');

                //Chiave e nome sono univoci nello stesso regolamento
                $table->unique([
                    'ruleset_id',
                    'key',
                ], 'weapon_properties_ruleset_key_unique');

                $table->unique([
                    'ruleset_id',
                    'name',
                ], 'weapon_properties_ruleset_name_unique');

                $table->index([
                    'ruleset_id',
                    'sort_order',
                ], 'weapon_properties_ruleset_sort_index');
            }
        );

        //Completa il catalogo delle competenze nelle armi
        Schema::table(
            'weapon_proficiencies',
            function (Blueprint $table) {
                $table->dropUnique(
                    'weapon_proficiencies_name_unique'
                );

                $table->foreignId('ruleset_id')
                    ->after('id')
                    ->constrained('rulesets')
                    ->cascadeOnDelete();

                $table->string('key')
                    ->after('ruleset_id');

                $table->unsignedSmallInteger('sort_order')
                    ->default(0)
                    ->after('description');

                $table->unique([
                    'ruleset_id',
                    'key',
                ], 'weapon_proficiencies_ruleset_key_unique');

                $table->unique([
                    'ruleset_id',
                    'name',
                ], 'weapon_proficiencies_ruleset_name_unique');

                $table->index([
                    'ruleset_id',
                    'type',
                    'sort_order',
                ], 'weapon_proficiencies_ruleset_type_sort_index');
            }
        );

        //Completa il catalogo delle competenze nelle armature
        Schema::table(
            'armor_proficiencies',
            function (Blueprint $table) {
                $table->dropUnique(
                    'armor_proficiencies_name_unique'
                );

                $table->foreignId('ruleset_id')
                    ->after('id')
                    ->constrained('rulesets')
                    ->cascadeOnDelete();

                $table->string('key')
                    ->after('ruleset_id');

                $table->unsignedSmallInteger('sort_order')
                    ->default(0)
                    ->after('description');

                $table->unique([
                    'ruleset_id',
                    'key',
                ], 'armor_proficiencies_ruleset_key_unique');

                $table->unique([
                    'ruleset_id',
                    'name',
                ], 'armor_proficiencies_ruleset_name_unique');

                $table->index([
                    'ruleset_id',
                    'type',
                    'sort_order',
                ], 'armor_proficiencies_ruleset_type_sort_index');
            }
        );

        //Completa il catalogo delle competenze negli strumenti
        Schema::table(
            'tool_proficiencies',
            function (Blueprint $table) {
                $table->dropUnique(
                    'tool_proficiencies_name_unique'
                );

                $table->foreignId('ruleset_id')
                    ->after('id')
                    ->constrained('rulesets')
                    ->cascadeOnDelete();

                $table->string('key')
                    ->after('ruleset_id');

                $table->unsignedSmallInteger('sort_order')
                    ->default(0)
                    ->after('description');

                $table->unique([
                    'ruleset_id',
                    'key',
                ], 'tool_proficiencies_ruleset_key_unique');

                $table->unique([
                    'ruleset_id',
                    'name',
                ], 'tool_proficiencies_ruleset_name_unique');

                $table->index([
                    'ruleset_id',
                    'type',
                    'sort_order',
                ], 'tool_proficiencies_ruleset_type_sort_index');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'tool_proficiencies',
            function (Blueprint $table) {
                $table->dropIndex(
                    'tool_proficiencies_ruleset_type_sort_index'
                );

                $table->dropUnique(
                    'tool_proficiencies_ruleset_key_unique'
                );

                $table->dropUnique(
                    'tool_proficiencies_ruleset_name_unique'
                );

                $table->dropConstrainedForeignId('ruleset_id');

                $table->dropColumn([
                    'key',
                    'sort_order',
                ]);

                $table->unique('name');
            }
        );

        Schema::table(
            'armor_proficiencies',
            function (Blueprint $table) {
                $table->dropIndex(
                    'armor_proficiencies_ruleset_type_sort_index'
                );

                $table->dropUnique(
                    'armor_proficiencies_ruleset_key_unique'
                );

                $table->dropUnique(
                    'armor_proficiencies_ruleset_name_unique'
                );

                $table->dropConstrainedForeignId('ruleset_id');

                $table->dropColumn([
                    'key',
                    'sort_order',
                ]);

                $table->unique('name');
            }
        );

        Schema::table(
            'weapon_proficiencies',
            function (Blueprint $table) {
                $table->dropIndex(
                    'weapon_proficiencies_ruleset_type_sort_index'
                );

                $table->dropUnique(
                    'weapon_proficiencies_ruleset_key_unique'
                );

                $table->dropUnique(
                    'weapon_proficiencies_ruleset_name_unique'
                );

                $table->dropConstrainedForeignId('ruleset_id');

                $table->dropColumn([
                    'key',
                    'sort_order',
                ]);

                $table->unique('name');
            }
        );

        Schema::table(
            'weapon_properties',
            function (Blueprint $table) {
                $table->dropIndex(
                    'weapon_properties_ruleset_sort_index'
                );

                $table->dropUnique(
                    'weapon_properties_ruleset_key_unique'
                );

                $table->dropUnique(
                    'weapon_properties_ruleset_name_unique'
                );

                $table->dropConstrainedForeignId('ruleset_id');

                $table->dropColumn([
                    'key',
                    'sort_order',
                ]);

                $table->unique('name');
            }
        );

        Schema::table(
            'item_armor_profiles',
            function (Blueprint $table) {
                $table->dropIndex(
                    'item_armor_profiles_category_index'
                );

                $table->dropColumn('armor_category');
            }
        );

        Schema::table(
            'item_weapon_profiles',
            function (Blueprint $table) {
                $table->dropIndex(
                    'item_weapon_profiles_category_index'
                );

                $table->dropColumn('weapon_category');
            }
        );

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(
                'items_ruleset_type_sort_index'
            );

            $table->dropColumn('sort_order');
        });

        Schema::table('item_types', function (Blueprint $table) {
            $table->dropUnique(
                'item_types_key_unique'
            );

            $table->dropUnique(
                'item_types_name_unique'
            );

            $table->dropColumn([
                'key',
                'sort_order',
            ]);
        });
    }
};
