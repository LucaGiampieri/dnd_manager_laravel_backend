<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Rende esplicita l'unità delle velocità e rende estensibili le scelte
    public function up(): void
    {
        //Rinomina la velocità delle razze specificando che è espressa in metri
        Schema::table(
            'race_movements',
            function (Blueprint $table) {
                $table->renameColumn(
                    'speed',
                    'speed_meters'
                );
            }
        );

        //Rinomina la velocità delle sottorazze specificando che è espressa in metri
        Schema::table(
            'subrace_movements',
            function (Blueprint $table) {
                $table->renameColumn(
                    'speed',
                    'speed_meters'
                );
            }
        );

        //Trasforma gli enum delle scelte in stringhe estensibili
        foreach ($this->choiceColumns() as $choiceColumn) {
            Schema::table(
                $choiceColumn['table'],
                function (Blueprint $table) use ($choiceColumn) {
                    //Una stringa permette di aggiungere nuovi tipi
                    //senza creare una migrazione per ogni nuovo contenuto
                    $table->string(
                        $choiceColumn['column'],
                        50
                    )->change();
                }
            );
        }
    }

    //Ripristina i nomi e i tipi originali delle colonne
    public function down(): void
    {
        //Prepara i dati per il ripristino degli enum originali
        foreach ($this->choiceColumns() as $choiceColumn) {
            //I tipi aggiunti successivamente non appartengono
            //all'enum originale e vengono convertiti in "other"
            DB::table($choiceColumn['table'])
                ->whereNotIn(
                    $choiceColumn['column'],
                    $this->originalChoiceTypes()
                )
                ->update([
                    $choiceColumn['column'] => 'other',
                ]);

            Schema::table(
                $choiceColumn['table'],
                function (Blueprint $table) use ($choiceColumn) {
                    //Ripristina l'elenco chiuso dei tipi originali
                    $table->enum(
                        $choiceColumn['column'],
                        $this->originalChoiceTypes()
                    )->change();
                }
            );
        }

        //Ripristina il nome originale della velocità delle sottorazze
        Schema::table(
            'subrace_movements',
            function (Blueprint $table) {
                $table->renameColumn(
                    'speed_meters',
                    'speed'
                );
            }
        );

        //Ripristina il nome originale della velocità delle razze
        Schema::table(
            'race_movements',
            function (Blueprint $table) {
                $table->renameColumn(
                    'speed_meters',
                    'speed'
                );
            }
        );
    }

    //Restituisce le tabelle e le colonne che contengono i tipi di scelta
    private function choiceColumns(): array
    {
        return [
            [
                'table' => 'race_choices',
                'column' => 'choice_type',
            ],
            [
                'table' => 'subrace_choices',
                'column' => 'choice_type',
            ],
            [
                'table' => 'race_choice_options',
                'column' => 'option_type',
            ],
            [
                'table' => 'subrace_choice_options',
                'column' => 'option_type',
            ],
        ];
    }

    //Restituisce i tipi previsti dalle vecchie colonne enum
    private function originalChoiceTypes(): array
    {
        return [
            'ability',
            'skill',
            'language',
            'weapon_proficiency',
            'armor_proficiency',
            'tool_proficiency',
            'feature',
            'item',
            'size',
            'sense',
            'movement_type',
            'damage_type',
            'other',
        ];
    }
};
