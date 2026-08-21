<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea le strutture specifiche degli oggetti magici
    public function up(): void
    {
        //Contiene metadati come sintonia, maledizioni e oggetto base
        Schema::create('item_magic_profiles', function (Blueprint $table) {
            $table->id();

            //Oggetto magico proprietario del profilo
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Oggetto comune da cui deriva la versione magica
            $table->foreignId('base_item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();

            //Requisiti specifici necessari per la sintonia
            $table->text('attunement_requirement')
                ->nullable();

            //Indica se l'oggetto possiede una maledizione
            $table->boolean('is_cursed')
                ->default(false);

            //Momento in cui la maledizione può essere conosciuta
            $table->enum('curse_disclosure', [
                'known',
                'on_attunement',
                'hidden',
                'special',
            ])->nullable();

            //Condizione speciale necessaria per distruggere l'oggetto
            $table->text('destruction_condition')
                ->nullable();

            //Regole speciali non rappresentabili tramite modificatori
            $table->text('special_rules')
                ->nullable();

            //Note interne aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni oggetto possiede un solo profilo magico
            $table->unique(
                'item_id',
                'item_magic_profiles_item_unique'
            );

            //Velocizza la ricerca delle versioni magiche di un oggetto
            $table->index(
                'base_item_id',
                'item_magic_profiles_base_item_index'
            );
        });

        //Contiene cariche, utilizzi, dosi e relativo recupero
        Schema::create('item_resources', function (Blueprint $table) {
            $table->id();

            //Oggetto che possiede la risorsa
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Chiave tecnica della risorsa
            $table->string('key');

            //Nome pubblico della risorsa
            $table->string('name');

            //Tipologia di risorsa consumabile
            $table->enum('resource_type', [
                'charges',
                'uses',
                'doses',
                'special',
            ])->default('charges');

            //Valore massimo della risorsa
            $table->unsignedInteger('maximum');

            //Quantità normalmente consumata per utilizzo
            $table->unsignedInteger('expended_per_use')
                ->default(1);

            //Momento o metodo di recupero
            $table->enum('recharge_type', [
                'none',
                'dawn',
                'dusk',
                'short_rest',
                'long_rest',
                'manual',
                'special',
            ])->default('none');

            //Indica che vengono recuperate tutte le risorse
            $table->boolean('recharge_all')
                ->default(false);

            //Quantità fissa recuperata
            $table->unsignedInteger('recharge_fixed')
                ->nullable();

            //Numero di dadi utilizzati per il recupero
            $table->unsignedSmallInteger('recharge_dice_count')
                ->nullable();

            //Dimensione dei dadi utilizzati per il recupero
            $table->unsignedSmallInteger('recharge_die_size')
                ->nullable();

            //Bonus o malus applicato al tiro di recupero
            $table->integer('recharge_bonus')
                ->default(0);

            //Comportamento quando la risorsa raggiunge zero
            $table->enum('empty_behavior', [
                'inactive',
                'consume',
                'destroy',
                'roll_destroy',
                'special',
            ])->default('inactive');

            //Regola applicata quando la risorsa si esaurisce
            $table->text('empty_behavior_condition')
                ->nullable();

            //Descrizione pubblica della risorsa
            $table->text('description')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note interne aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate sullo stesso oggetto
            $table->unique(
                [
                    'item_id',
                    'key',
                ],
                'item_resources_item_key_unique'
            );

            //Velocizza il recupero delle risorse dell'oggetto
            $table->index(
                [
                    'item_id',
                    'sort_order',
                ],
                'item_resources_item_order_index'
            );
        });
    }

    //Rimuove le strutture degli oggetti magici
    public function down(): void
    {
        Schema::dropIfExists('item_resources');
        Schema::dropIfExists('item_magic_profiles');
    }
};
