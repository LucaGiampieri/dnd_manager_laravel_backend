<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea i profili di utilizzo degli oggetti
    public function up(): void
    {
        //Crea le regole dei consumabili
        Schema::create(
            'item_consumable_profiles',
            function (Blueprint $table) {
                $table->id();

                //Oggetto consumabile a cui appartiene il profilo
                $table->foreignId('item_id')
                    ->constrained('items')
                    ->cascadeOnDelete();

                //Modalità con cui viene utilizzato il consumabile
                $table->enum('activation_type', [
                    'drink',
                    'eat',
                    'apply',
                    'read',
                    'throw',
                    'inhale',
                    'special',
                ]);

                //Tipo di azione richiesta
                $table->enum('activation_action', [
                    'action',
                    'bonus_action',
                    'reaction',
                    'minute',
                    'hour',
                    'special',
                ])->default('action');

                //Numero di azioni, minuti o ore necessari
                $table->unsignedSmallInteger('activation_value')
                    ->default(1);

                //Bersaglio sul quale può essere utilizzato
                $table->enum('target_scope', [
                    'self',
                    'creature',
                    'object',
                    'point',
                    'self_or_creature',
                    'special',
                ])->default('self');

                //Numero di utilizzi contenuti in un singolo oggetto
                $table->unsignedSmallInteger('uses_per_item')
                    ->default(1);

                //Indica se un utilizzo consuma definitivamente una dose
                $table->boolean('consumed_on_use')
                    ->default(true);

                //Indica se rimane un contenitore non magico
                $table->boolean('leaves_container')
                    ->default(false);

                //Regole particolari del consumabile
                $table->text('special_rules')
                    ->nullable();

                //Informazioni aggiuntive
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Ogni oggetto possiede un solo profilo da consumabile
                $table->unique('item_id');
            }
        );

        //Crea i profili dei contenitori
        Schema::create(
            'item_container_profiles',
            function (Blueprint $table) {
                $table->id();

                //Oggetto che funge da contenitore
                $table->foreignId('item_id')
                    ->constrained('items')
                    ->cascadeOnDelete();

                //Peso massimo trasportabile espresso in chilogrammi
                $table->decimal(
                    'capacity_weight_kg',
                    10,
                    3
                )->nullable();

                //Volume massimo espresso in litri
                $table->decimal(
                    'capacity_volume_liters',
                    12,
                    3
                )->nullable();

                //Indica se il contenuto non aumenta il peso esterno
                $table->boolean('ignores_contents_weight')
                    ->default(false);

                //Indica la presenza di uno spazio extradimensionale
                $table->boolean('is_extradimensional')
                    ->default(false);

                //Azione richiesta per recuperare un oggetto
                $table->enum('retrieval_action', [
                    'object_interaction',
                    'action',
                    'bonus_action',
                    'special',
                ])->default('object_interaction');

                //Descrizione delle dimensioni esterne e interne
                $table->text('dimensions')
                    ->nullable();

                //Regole per le creature viventi al suo interno
                $table->text('living_creature_rules')
                    ->nullable();

                //Conseguenze di sovraccarico, rottura o perforazione
                $table->text('rupture_rules')
                    ->nullable();

                //Interazioni con altri spazi extradimensionali
                $table->text('nesting_rules')
                    ->nullable();

                //Informazioni aggiuntive
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Ogni oggetto possiede un solo profilo da contenitore
                $table->unique('item_id');
            }
        );

        //Crea le regole di applicabilità dei modelli magici
        Schema::create(
            'item_magic_applicabilities',
            function (Blueprint $table) {
                $table->id();

                //Oggetto magico generico a cui appartiene la regola
                $table->foreignId('item_id')
                    ->constrained('items')
                    ->cascadeOnDelete();

                //Chiave tecnica della regola
                $table->string('key');

                //Tipologia di oggetti a cui può essere applicato
                $table->enum('target_scope', [
                    'specific_item',
                    'item_type',
                    'any_weapon',
                    'any_armor',
                    'weapon_category',
                    'armor_category',
                    'special',
                ]);

                //Eventuale oggetto base specifico
                $table->foreignId('target_item_id')
                    ->nullable()
                    ->constrained('items')
                    ->nullOnDelete();

                //Eventuale tipologia base richiesta
                $table->foreignId('target_item_type_id')
                    ->nullable()
                    ->constrained('item_types')
                    ->nullOnDelete();

                //Eventuale categoria di arma richiesta
                $table->enum('weapon_category', [
                    'simple',
                    'martial',
                ])->nullable();

                //Eventuale categoria di armatura richiesta
                $table->enum('armor_category', [
                    'light',
                    'medium',
                    'heavy',
                    'shield',
                ])->nullable();

                //Richiede che l'oggetto base non sia già magico
                $table->boolean('requires_nonmagical')
                    ->default(true);

                //Condizione aggiuntiva di applicabilità
                $table->text('condition')
                    ->nullable();

                //Ordine di valutazione della regola
                $table->unsignedSmallInteger('sort_order')
                    ->default(0);

                //Informazioni aggiuntive
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Evita chiavi duplicate nello stesso modello magico
                $table->unique(
                    [
                        'item_id',
                        'key',
                    ],
                    'item_magic_applicabilities_item_key_unique'
                );

                //Velocizza la ricerca delle regole per ambito
                $table->index(
                    [
                        'target_scope',
                        'target_item_type_id',
                    ],
                    'item_magic_applicabilities_scope_index'
                );
            }
        );
    }

    //Rimuove i profili di utilizzo in ordine inverso
    public function down(): void
    {
        Schema::dropIfExists('item_magic_applicabilities');
        Schema::dropIfExists('item_container_profiles');
        Schema::dropIfExists('item_consumable_profiles');
    }
};
