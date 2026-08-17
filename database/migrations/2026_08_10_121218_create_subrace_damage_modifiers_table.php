<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_damage_modifiers', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente questa resistenza, immunità o vulnerabilità
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica applicata al danno
            $table->enum('modifier', [
                'resistance',
                'immunity',
                'vulnerability'
            ]);

            //Eventuale condizione necessaria perché la modifica si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita duplicati per la stessa sottorazza
            $table->unique([
                'subrace_id',
                'damage_type_id',
                'modifier'
            ], 'uq_subrace_damage_modifiers_subrace_id_damage_type_id_m_0cae7cf0');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_damage_modifiers');
    }
};
