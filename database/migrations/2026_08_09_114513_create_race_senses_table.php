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
        Schema::create('race_senses', function (Blueprint $table) {

            $table->id();

            //Razza che possiede automaticamente questo senso
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di senso
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Portata del senso in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Eventuali condizioni o limitazioni
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può avere due volte lo stesso senso
            $table->unique([
                'race_id',
                'sense_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_senses');
    }
};
