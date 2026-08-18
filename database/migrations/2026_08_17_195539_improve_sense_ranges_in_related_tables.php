<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        foreach ($this->senseTables() as $tableName) {
            if (Schema::hasColumn($tableName, 'range')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('range_meters', 8, 3)
                        ->unsigned()
                        ->nullable();
                });

                DB::table($tableName)->update([
                    'range_meters' => DB::raw('`range`'),
                ]);

                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('range');
                });
            } elseif (Schema::hasColumn($tableName, 'range_meters')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('temporary_range_meters', 8, 3)
                        ->unsigned()
                        ->nullable();
                });

                DB::table($tableName)->update([
                    'temporary_range_meters' => DB::raw('`range_meters`'),
                ]);

                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('range_meters');
                });

                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('range_meters', 8, 3)
                        ->unsigned()
                        ->nullable();
                });

                DB::table($tableName)->update([
                    'range_meters' => DB::raw('`temporary_range_meters`'),
                ]);

                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('temporary_range_meters');
                });
            } else {
                throw new \RuntimeException(
                    "Nessuna colonna del raggio trovata nella tabella {$tableName}."
                );
            }

            if (! Schema::hasColumn($tableName, 'is_blind_beyond_range')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->boolean('is_blind_beyond_range')
                        ->default(false);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->senseTables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->float('range')
                    ->nullable();
            });

            DB::table($tableName)->update([
                'range' => DB::raw('`range_meters`'),
            ]);

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'range_meters',
                    'is_blind_beyond_range',
                ]);
            });
        }
    }

    private function senseTables(): array
    {
        return [
            'race_senses',
            'subrace_senses',
            'character_senses',
            'creature_stat_block_senses',
        ];
    }
};
