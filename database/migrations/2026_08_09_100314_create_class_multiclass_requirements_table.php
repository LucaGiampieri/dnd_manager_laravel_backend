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
        Schema::create('class_multiclass_requirements', function (Blueprint $table) {

            $table->id();

            //Classe alla quale si vuole accedere tramite multiclasse
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            //Gruppo logico del requisito:
            //Requisiti appartenenti allo stesso gruppo sono considerati alternative (OR)
            //Gruppi differenti devono invece essere soddisfatti tutti (AND)
            $table->unsignedTinyInteger('requirement_group')
                ->default(1);

            //Caratteristica richiesta
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Valore minimo richiesto
            $table->unsignedTinyInteger('minimum_value');

            //Eventuali note sul requisito
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso requisito nello stesso gruppo
            $table->unique([
                'class_id',
                'requirement_group',
                'ability_id'
            ], 'uq_class_multiclass_requirements_class_id_requirement_g_2e29cf13');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_multiclass_requirements');
    }
};
