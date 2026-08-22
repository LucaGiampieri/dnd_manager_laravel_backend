<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Crea la tabella che collega gli oggetti agli incantesimi
    public function up(): void
    {
        Schema::create('item_spell_castings', function (Blueprint $table) {
            $table->id();

            //Oggetto che permette di lanciare l'incantesimo
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Incantesimo concesso o lanciato dall'oggetto
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Eventuale risorsa dell'oggetto consumata dal lancio
            $table->foreignId('item_resource_id')
                ->nullable()
                ->constrained('item_resources')
                ->cascadeOnDelete();

            //Chiave tecnica univoca all'interno dell'oggetto
            $table->string('key');

            //Tempo richiesto per attivare il lancio
            $table->enum('activation_type', [
                'spell_casting_time',
                'action',
                'bonus_action',
                'reaction',
                'minute',
                'hour',
                'special',
            ])->default('spell_casting_time');

            //Numero di azioni, minuti o ore richiesti
            $table->unsignedSmallInteger('activation_value')
                ->default(1);

            //Quantità della risorsa consumata dal lancio
            $table->unsignedSmallInteger('resource_cost')
                ->default(0);

            //Livello al quale viene lanciato l'incantesimo
            $table->unsignedTinyInteger('cast_at_level')
                ->nullable();

            //CD fissa eventualmente stabilita dall'oggetto
            $table->unsignedTinyInteger('save_dc')
                ->nullable();

            //Bonus fisso all'attacco con incantesimo
            $table->smallInteger('spell_attack_bonus')
                ->nullable();

            //Indica se il lancio richiede le componenti dell'incantesimo
            $table->boolean('requires_components')
                ->default(false);

            //Sovrascrive l'eventuale requisito di concentrazione
            //Null indica che viene utilizzata la regola dell'incantesimo
            $table->boolean('requires_concentration')
                ->nullable();

            //Condizione necessaria per utilizzare il lancio
            $table->text('condition')
                ->nullable();

            //Descrizione sintetica dell'attivazione
            $table->text('description')
                ->nullable();

            //Ordine di visualizzazione nell'oggetto
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Informazioni aggiuntive non strutturate
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso oggetto
            $table->unique(
                [
                    'item_id',
                    'key',
                ],
                'item_spell_castings_item_key_unique'
            );

            //Velocizza la ricerca degli oggetti collegati a un incantesimo
            $table->index(
                [
                    'spell_id',
                    'sort_order',
                ],
                'item_spell_castings_spell_index'
            );
        });
    }

    //Rimuove la tabella dei lanci concessi dagli oggetti
    public function down(): void
    {
        Schema::dropIfExists('item_spell_castings');
    }
};
