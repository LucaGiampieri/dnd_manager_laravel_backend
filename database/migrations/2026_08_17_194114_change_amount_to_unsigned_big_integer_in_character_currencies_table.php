<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(
            'character_currencies',
            function (Blueprint $table) {
                $table->unsignedBigInteger('amount')
                    ->default(0)
                    ->change();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'character_currencies',
            function (Blueprint $table) {
                $table->decimal('amount', 12, 2)
                    ->default(0)
                    ->change();
            }
        );
    }
};
