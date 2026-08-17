<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('weapon_properties', function (Blueprint $table) {

            $table->id();

            //Nome della proprietà dell'arma
            $table->string('name')
                ->unique();

            //Descrizione della proprietà
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weapon_properties');
    }
};
