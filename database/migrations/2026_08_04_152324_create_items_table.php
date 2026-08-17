<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene l'oggetto
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata da seeder e API
            $table->string('key');

            //Nome dell'oggetto
            $table->string('name');

            //Categoria generale dell'oggetto
            $table->foreignId('item_type_id')
                ->constrained('item_types')
                ->cascadeOnDelete();

            //Descrizione generale dell'oggetto
            $table->text('description')
                ->nullable();

            //Peso dell'oggetto in chilogrammi
            $table->decimal('weight_kg', 10, 3)
                ->nullable();

            //Indica se più copie possono essere gestite come una quantità unica
            $table->boolean('is_stackable')
                ->default(false);

            //Rarità dell'oggetto
            $table->enum('rarity', [
                'common',
                'uncommon',
                'rare',
                'very_rare',
                'legendary',
                'artifact'
            ])
                ->nullable();

            //Indica se l'oggetto è magico
            $table->boolean('is_magical')
                ->default(false);

            //Indica se l'oggetto richiede sintonia
            $table->boolean('requires_attunement')
                ->default(false);

            //Eventuali requisiti necessari per utilizzare o sintonizzarsi con l'oggetto
            $table->text('requirements')
                ->nullable();

            //Eventuali note generali
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'items_ruleset_key_unique');

            $table->index([
                'ruleset_id',
                'name',
            ], 'items_ruleset_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
