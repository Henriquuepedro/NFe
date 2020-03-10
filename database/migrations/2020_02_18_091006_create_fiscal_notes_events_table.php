<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiscalNotesEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiscal_notes_events', function (Blueprint $table) {
            $table->integer('cod_nf');
            $table->dateTime('data_cancela');
            $table->string('motivo_cancela', 256);
            $table->integer('seq');
            $table->string('chave', 44);
            $table->string('protocolo', 15);
            $table->string('tipo_evento', 6);
            $table->integer('cod_user_reg')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            $table->primary('cod_nf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiscal_notes_events');
    }
}
