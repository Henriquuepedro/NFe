<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->integer('codigo_ibge');
            $table->primary('codigo_ibge');
            $table->string('nome', 100);
            $table->string('latitude', 32);
            $table->string('longitude', 32);
            $table->boolean('capital');
            $table->integer('codigo_uf')->references('codigo_uf')->on('states');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
