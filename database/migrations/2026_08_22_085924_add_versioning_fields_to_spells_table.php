<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge il versionamento al catalogo degli incantesimi
    public function up(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            //Identità condivisa dalle diverse versioni
            $table->string('canonical_key')
                ->nullable()
                ->after('key');

            //Manuale o revisione che definisce questa versione
            $table->string('version_key')
                ->default('custom')
                ->after('canonical_key');

            //Indica se la versione è stata sostituita
            $table->boolean('is_legacy')
                ->default(false)
                ->after('version_key');
        });

        //Valorizza eventuali incantesimi già presenti
        DB::table('spells')
            ->select([
                'id',
                'key',
            ])
            ->orderBy('id')
            ->each(function (object $spell): void {
                DB::table('spells')
                    ->where('id', $spell->id)
                    ->update([
                        'canonical_key' => $spell->key,
                    ]);
            });

        //Rende obbligatoria la chiave canonica dopo il riempimento
        Schema::table('spells', function (Blueprint $table) {
            $table->string('canonical_key')
                ->nullable(false)
                ->change();

            //Velocizza la ricerca di tutte le versioni
            $table->index(
                [
                    'ruleset_id',
                    'canonical_key',
                ],
                'spells_ruleset_canonical_index'
            );

            //Evita due versioni uguali dello stesso incantesimo
            $table->unique(
                [
                    'ruleset_id',
                    'canonical_key',
                    'version_key',
                ],
                'spells_ruleset_canonical_version_unique'
            );
        });
    }

    //Rimuove il versionamento degli incantesimi
    public function down(): void
    {
        Schema::table('spells', function (Blueprint $table) {
            //Rimuove prima indici e vincoli
            $table->dropUnique(
                'spells_ruleset_canonical_version_unique'
            );

            $table->dropIndex(
                'spells_ruleset_canonical_index'
            );

            //Rimuove i campi di versionamento
            $table->dropColumn([
                'canonical_key',
                'version_key',
                'is_legacy',
            ]);
        });
    }
};
