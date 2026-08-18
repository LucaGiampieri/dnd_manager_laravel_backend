<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->renameColumn(
                'conversion_rate',
                'value_in_copper_pieces'
            );
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->unsignedInteger('value_in_copper_pieces')
                ->default(1)
                ->change();

            // Ordine crescente delle denominazioni
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            // Peso indicativo di una singola moneta in chilogrammi
            $table->decimal('coin_weight_kg', 8, 4)
                ->default('0.0100');

            // Rame, argento e oro sono le monete più comuni
            $table->boolean('is_common')
                ->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn([
                'sort_order',
                'coin_weight_kg',
                'is_common',
            ]);

            $table->decimal(
                'value_in_copper_pieces',
                12,
                4
            )
                ->default(1)
                ->change();
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->renameColumn(
                'value_in_copper_pieces',
                'conversion_rate'
            );
        });
    }
};
