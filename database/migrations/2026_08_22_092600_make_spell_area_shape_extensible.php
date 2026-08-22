<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Trasforma la forma dell'area in una stringa estensibile
    public function up(): void
    {
        Schema::table(
            'spell_target_profiles',
            function (Blueprint $table): void {
                //Una stringa permette di aggiungere nuove forme
                //senza modificare nuovamente la struttura del database
                $table->string('area_shape')
                    ->nullable()
                    ->change();
            }
        );
    }

    //Ripristina il campo limitato alle forme attualmente conosciute
    public function down(): void
    {
        Schema::table(
            'spell_target_profiles',
            function (Blueprint $table): void {
                $table->enum('area_shape', [
                    'cone',
                    'cube',
                    'cylinder',
                    'line',
                    'sphere',
                    'hemisphere',
                    'wall',
                    'emanation',
                    'square',
                    'rectangle',
                    'circle',
                    'special',
                ])
                    ->nullable()
                    ->change();
            }
        );
    }
};
