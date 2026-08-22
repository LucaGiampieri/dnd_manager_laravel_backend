<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class () extends Migration {
    //Aggiunge una chiave tecnica stabile alle scuole di magia
    public function up(): void
    {
        Schema::table('spell_schools', function (Blueprint $table): void {
            //La colonna nasce nullable per permettere il recupero dei dati esistenti
            $table->string('key')
                ->nullable()
                ->after('id');
        });

        //Associa i nomi italiani e inglesi alle chiavi tecniche ufficiali
        $officialKeys = [
            'abiurazione' => 'abjuration',
            'abjuration' => 'abjuration',
            'ammaliamento' => 'enchantment',
            'enchantment' => 'enchantment',
            'divinazione' => 'divination',
            'divination' => 'divination',
            'evocazione' => 'conjuration',
            'conjuration' => 'conjuration',
            'illusione' => 'illusion',
            'illusion' => 'illusion',
            'invocazione' => 'evocation',
            'evocation' => 'evocation',
            'necromanzia' => 'necromancy',
            'necromancy' => 'necromancy',
            'trasmutazione' => 'transmutation',
            'transmutation' => 'transmutation',
        ];

        //Recupera anche eventuali scuole già presenti nel database locale
        DB::table('spell_schools')
            ->orderBy('id')
            ->get([
                'id',
                'name',
            ])
            ->each(function (object $school) use ($officialKeys): void {
                $normalizedName = Str::lower(
                    Str::ascii($school->name)
                );

                //Usa la chiave ufficiale oppure genera una chiave di sicurezza
                $key = $officialKeys[$normalizedName]
                    ?? Str::snake(Str::ascii($school->name));

                DB::table('spell_schools')
                    ->where('id', $school->id)
                    ->update([
                        'key' => $key,
                    ]);
            });

        Schema::table('spell_schools', function (Blueprint $table): void {
            //Dopo il recupero la chiave diventa obbligatoria
            $table->string('key')
                ->nullable(false)
                ->change();

            //Impedisce la creazione di due scuole con la stessa chiave
            $table->unique(
                'key',
                'spell_schools_key_unique'
            );
        });
    }

    //Rimuove la chiave tecnica
    public function down(): void
    {
        Schema::table('spell_schools', function (Blueprint $table): void {
            $table->dropUnique('spell_schools_key_unique');
            $table->dropColumn('key');
        });
    }
};
