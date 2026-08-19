<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea il catalogo dei gradi di sfida del regolamento
    public function up(): void
    {
        Schema::create(
            'challenge_ratings',
            function (Blueprint $table) {
                $table->id();

                //Regolamento a cui appartiene il grado di sfida
                $table->foreignId('ruleset_id')
                    ->constrained('rulesets')
                    ->cascadeOnDelete();

                //Chiave tecnica stabile usata da seeder e API
                $table->string('key', 20);

                //Valore mostrato all'utente, comprese le frazioni
                $table->string('label', 10);

                //Valore numerico utilizzato per confronti e calcoli
                $table->decimal('numeric_value', 6, 3)
                    ->unsigned();

                //Bonus di competenza determinato dal grado di sfida
                $table->unsignedTinyInteger('proficiency_bonus');

                //Punti esperienza normalmente assegnati
                $table->unsignedInteger('experience_points');

                //Ordine corretto di visualizzazione
                $table->unsignedTinyInteger('sort_order');

                //Eventuali eccezioni o spiegazioni
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Evita chiavi duplicate nello stesso regolamento
                $table->unique(
                    [
                        'ruleset_id',
                        'key',
                    ],
                    'challenge_ratings_ruleset_key_unique'
                );

                //Evita due gradi di sfida con lo stesso valore
                $table->unique(
                    [
                        'ruleset_id',
                        'numeric_value',
                    ],
                    'challenge_ratings_ruleset_value_unique'
                );

                //Evita posizioni duplicate nell'ordinamento
                $table->unique(
                    [
                        'ruleset_id',
                        'sort_order',
                    ],
                    'challenge_ratings_ruleset_order_unique'
                );
            }
        );
    }

    //Elimina il catalogo dei gradi di sfida
    public function down(): void
    {
        Schema::dropIfExists('challenge_ratings');
    }
};
