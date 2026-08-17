<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('item_weapon_properties', function (Blueprint $table) {

            $table->id();

            //Arma a cui appartiene la proprietà
            $table->foreignId('item_weapon_profile_id')
                ->constrained('item_weapon_profiles')
                ->cascadeOnDelete();

            //Proprietà applicata all'arma
            $table->foreignId('weapon_property_id')
                ->constrained('weapon_properties')
                ->cascadeOnDelete();

            //Eventuale valore numerico associato alla proprietà
            $table->float('value')
                ->nullable();

            //Eventuale valore testuale
            $table->string('value_text')
                ->nullable();

            //Eventuali note specifiche di questa proprietà sull'arma
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa proprietà non può essere assegnata due volte alla stessa arma
            $table->unique([
                'item_weapon_profile_id',
                'weapon_property_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_weapon_properties');
    }
};
