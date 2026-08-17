<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('item_weapon_damages', function (Blueprint $table) {

            $table->id();

            //Profilo dell'arma a cui appartiene questo componente di danno
            $table->foreignId('item_weapon_profile_id')
                ->constrained('item_weapon_profiles')
                ->cascadeOnDelete();

            //Tipo di danno inflitto
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Numero di dadi base del danno
            $table->unsignedSmallInteger('dice_count');

            //Numero di facce del dado base
            $table->unsignedSmallInteger('die_size');

            //Eventuale bonus fisso incorporato direttamente nell'arma
            $table->integer('bonus')
                ->default(0);

            //Indica se questo è il componente principale del danno dell'arma
            $table->boolean('primary')
                ->default(true);

            //Ordine dei diversi componenti di danno
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_weapon_damages');
    }
};
