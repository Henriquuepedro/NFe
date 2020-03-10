<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('locales');
        Schema::create('locales', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigIncrements('id')->references('cod_locale')->on('clients')->onDelete('cascade');
            $table->string('place', 255)->nullable();
            $table->string('number', 64)->nullable();
            $table->string('complement', 255)->nullable();
            $table->string('district', 255)->nullable();
            
            $table->integer('cod_ibge_city')->nullable()->unsigned();
            $table->foreign('cod_ibge_city')->references('codigo_ibge')->on('cities')->onDelete('cascade');

            $table->integer('cep');
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
        Schema::dropIfExists('locales');
    }
}
