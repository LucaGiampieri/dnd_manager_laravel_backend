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
        Schema::create('subrace_senses', function (Blueprint $table) {

            $table->id();

            //Sottorazza che possiede automaticamente questo senso
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di senso
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Portata del senso in metri
            $table->float('range')
                ->nullable();

            //Eventuali condizioni o limitazioni
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può avere due volte lo stesso senso
            $table->unique([
                'subrace_id',
                'sense_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subrace_senses');
    }
};
