<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        //Applica la stessa modifica a tutte le tabelle
        //che collegano un contenuto a un senso speciale
        foreach ($this->senseTables() as $tableName) {
            //Gestisce le tabelle che possiedono ancora
            //la vecchia colonna generica chiamata range
            if (Schema::hasColumn($tableName, 'range')) {
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        //Crea il nuovo raggio in metri
                        //con una precisione fino al millesimo
                        $table->decimal(
                            'range_meters',
                            8,
                            3
                        )
                            ->unsigned()
                            ->nullable();
                    }
                );

                //Copia i valori dalla vecchia colonna
                //alla nuova senza perdere i dati esistenti
                DB::table($tableName)->update([
                    'range_meters' => DB::raw('`range`'),
                ]);

                //Elimina la vecchia colonna dopo aver copiato i dati
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        $table->dropColumn('range');
                    }
                );
            } elseif (
                Schema::hasColumn($tableName, 'range_meters')
            ) {
                //Gestisce una tabella che possiede già range_meters,
                //ad esempio dopo un’esecuzione precedente rimasta parziale
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        //Crea una colonna temporanea con il tipo corretto
                        $table->decimal(
                            'temporary_range_meters',
                            8,
                            3
                        )
                            ->unsigned()
                            ->nullable();
                    }
                );

                //Conserva temporaneamente tutti i raggi esistenti
                DB::table($tableName)->update([
                    'temporary_range_meters' =>
                        DB::raw('`range_meters`'),
                ]);

                //Elimina la precedente versione di range_meters
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        $table->dropColumn('range_meters');
                    }
                );

                //Ricrea range_meters utilizzando
                //precisione, scala e vincoli corretti
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        $table->decimal(
                            'range_meters',
                            8,
                            3
                        )
                            ->unsigned()
                            ->nullable();
                    }
                );

                //Riporta i valori conservati nella colonna definitiva
                DB::table($tableName)->update([
                    'range_meters' =>
                        DB::raw('`temporary_range_meters`'),
                ]);

                //Rimuove la colonna temporanea ormai inutilizzata
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        $table->dropColumn(
                            'temporary_range_meters'
                        );
                    }
                );
            } else {
                //Interrompe la migrazione se la struttura trovata
                //non corrisponde a nessuno degli stati previsti
                throw new \RuntimeException(
                    "Nessuna colonna del raggio trovata "
                    . "nella tabella {$tableName}."
                );
            }

            //Aggiunge il campo soltanto se non esiste già,
            //rendendo la migrazione compatibile con stati parziali
            if (
                ! Schema::hasColumn(
                    $tableName,
                    'is_blind_beyond_range'
                )
            ) {
                Schema::table(
                    $tableName,
                    function (Blueprint $table) {
                        //Indica se la creatura è considerata cieca
                        //oltre il raggio fornito dal senso
                        $table->boolean(
                            'is_blind_beyond_range'
                        )->default(false);
                    }
                );
            }
        }
    }

    public function down(): void
    {
        //Ripristina la vecchia struttura
        //in tutte le tabelle interessate
        foreach ($this->senseTables() as $tableName) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    //Ricrea la precedente colonna generica del raggio
                    $table->float('range')
                        ->nullable();
                }
            );

            //Copia i valori espressi in metri
            //nella colonna precedente
            DB::table($tableName)->update([
                'range' => DB::raw('`range_meters`'),
            ]);

            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    //Elimina le due colonne aggiunte
                    //dalla migrazione
                    $table->dropColumn([
                        'range_meters',
                        'is_blind_beyond_range',
                    ]);
                }
            );
        }
    }

    private function senseTables(): array
    {
        //Elenca le tabelle alle quali devono essere
        //applicate le stesse modifiche strutturali
        return [
            'race_senses',
            'subrace_senses',
            'character_senses',
            'creature_stat_block_senses',
        ];
    }
};
