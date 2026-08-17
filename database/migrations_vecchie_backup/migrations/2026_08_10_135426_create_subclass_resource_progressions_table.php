<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_resource_progressions', function (Blueprint $table) {

            $table->id();

            //Risorsa della sottoclasse che sta progredendo
            $table->foreignId('subclass_resource_id')
                ->constrained('subclass_resources')
                ->cascadeOnDelete();

            //Livello della classe al quale si applica questa progressione
            $table->unsignedTinyInteger('level');

            //Proprietà della risorsa che cambia
            $table->string('key');

            //Valore numerico
            $table->integer('value')
                ->nullable();

            //Eventuale dado
            $table->string('dice')
                ->nullable();

            //Formula o valore testuale
            $table->string('value_text')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa proprietà della stessa risorsa non può essere definita due volte allo stesso livello
            $table->unique([
                'subclass_resource_id',
                'level',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_resource_progressions');
    }
};
