<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge i dati necessari alle scelte sostitutive
    public function up(): void
    {
        //Estende le scelte appartenenti alle razze
        Schema::table('race_choices', function (Blueprint $table) {
            //Capacità automatica eventualmente sostituita
            $table->foreignId('replaces_feature_id')
                ->nullable()
                ->after('choice_type')
                ->constrained('features')
                ->nullOnDelete();

            //Indica se la scelta richiede l'approvazione del DM
            $table->boolean('requires_dm_permission')
                ->default(false)
                ->after('required');
        });

        //Estende le opzioni appartenenti alle razze
        Schema::table(
            'race_choice_options',
            function (Blueprint $table) {
                //Ascendenza richiesta o rappresentata dall'opzione
                $table->string('ancestry_key')
                    ->nullable()
                    ->after('option_text');

                //Condizione necessaria per selezionare l'opzione
                $table->text('eligibility_condition')
                    ->nullable()
                    ->after('ancestry_key');

                //Velocizza il recupero delle opzioni per ascendenza
                $table->index(
                    [
                        'race_choice_id',
                        'ancestry_key',
                    ],
                    'race_choice_options_ancestry_index'
                );
            }
        );

        //Estende anche le scelte delle sottorazze
        Schema::table(
            'subrace_choices',
            function (Blueprint $table) {
                //Capacità automatica eventualmente sostituita
                $table->foreignId('replaces_feature_id')
                    ->nullable()
                    ->after('choice_type')
                    ->constrained('features')
                    ->nullOnDelete();

                //Indica se la scelta richiede l'approvazione del DM
                $table->boolean('requires_dm_permission')
                    ->default(false)
                    ->after('required');
            }
        );

        //Estende anche le opzioni delle sottorazze
        Schema::table(
            'subrace_choice_options',
            function (Blueprint $table) {
                //Ascendenza richiesta o rappresentata dall'opzione
                $table->string('ancestry_key')
                    ->nullable()
                    ->after('option_text');

                //Condizione necessaria per selezionare l'opzione
                $table->text('eligibility_condition')
                    ->nullable()
                    ->after('ancestry_key');

                //Velocizza il recupero delle opzioni per ascendenza
                $table->index(
                    [
                        'subrace_choice_id',
                        'ancestry_key',
                    ],
                    'subrace_choice_options_ancestry_index'
                );
            }
        );
    }

    //Rimuove i metadati aggiunti alle scelte
    public function down(): void
    {
        Schema::table(
            'subrace_choice_options',
            function (Blueprint $table) {
                $table->dropIndex(
                    'subrace_choice_options_ancestry_index'
                );

                $table->dropColumn([
                    'ancestry_key',
                    'eligibility_condition',
                ]);
            }
        );

        Schema::table(
            'subrace_choices',
            function (Blueprint $table) {
                $table->dropConstrainedForeignId(
                    'replaces_feature_id'
                );

                $table->dropColumn(
                    'requires_dm_permission'
                );
            }
        );

        Schema::table(
            'race_choice_options',
            function (Blueprint $table) {
                $table->dropIndex(
                    'race_choice_options_ancestry_index'
                );

                $table->dropColumn([
                    'ancestry_key',
                    'eligibility_condition',
                ]);
            }
        );

        Schema::table(
            'race_choices',
            function (Blueprint $table) {
                $table->dropConstrainedForeignId(
                    'replaces_feature_id'
                );

                $table->dropColumn(
                    'requires_dm_permission'
                );
            }
        );
    }
};
