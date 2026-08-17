<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_feature_progressions', function (Blueprint $table) {

            $table->id();

            //Collegamento alla capacità specifica concessa dalla sottoclasse
            $table->foreignId('subclass_feature_id')
                ->constrained('subclass_features')
                ->cascadeOnDelete();

            //Livello della classe al quale si applica questa progressione
            $table->unsignedTinyInteger('level');

            //Nome del parametro che sta cambiando
            $table->string('key');

            //Valore numerico della progressione
            $table->integer('value')
                ->nullable();

            //Eventuale dado associato alla progressione
            $table->string('dice')
                ->nullable();

            //Eventuale valore testuale
            $table->string('value_text')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa proprietà della stessa feature non può essere definita due volte allo stesso livello
            $table->unique([
                'subclass_feature_id',
                'level',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_feature_progressions');
    }
};
