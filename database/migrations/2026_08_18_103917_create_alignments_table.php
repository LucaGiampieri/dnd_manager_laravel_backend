<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('alignments', function (Blueprint $table) {
            $table->id();

            //Regolamento a cui appartiene l'allineamento
            $table->foreignId('ruleset_id')
                ->constrained()
                ->cascadeOnDelete();

            //Chiave tecnica stabile usata nei seeder e nel codice
            $table->string('key', 50);

            //Nome italiano dell'allineamento
            $table->string('name', 100);

            //Abbreviazione italiana, per esempio LB o CN
            $table->string('abbreviation', 3);

            //Posizione sull'asse tra ordine e libertà
            $table->enum('ethical_axis', [
                'lawful',
                'neutral',
                'chaotic',
            ]);

            //Posizione sull'asse tra bene e male
            $table->enum('moral_axis', [
                'good',
                'neutral',
                'evil',
            ]);

            //Descrizione generale e parafrasata dell'allineamento
            $table->text('description')
                ->nullable();

            //Ordine con cui mostrare gli allineamenti nell'interfaccia
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            $table->timestamps();

            //Impedisce di duplicare la stessa chiave nello stesso regolamento
            $table->unique([
                'ruleset_id',
                'key',
            ], 'alignments_ruleset_key_unique');

            //Impedisce di duplicare lo stesso nome nello stesso regolamento
            $table->unique([
                'ruleset_id',
                'name',
            ], 'alignments_ruleset_name_unique');

            //Impedisce di duplicare la stessa abbreviazione nello stesso regolamento
            $table->unique([
                'ruleset_id',
                'abbreviation',
            ], 'alignments_ruleset_abbreviation_unique');

            //Ogni combinazione dei due assi può esistere una sola volta per regolamento
            $table->unique([
                'ruleset_id',
                'ethical_axis',
                'moral_axis',
            ], 'alignments_ruleset_axes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alignments');
    }
};
