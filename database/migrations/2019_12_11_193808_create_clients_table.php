<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('razao_social', 255);
            $table->string('fantasia', 255)->nullable();
            $table->string('cnpj_cpf', 14)->nullable();
            $table->string('rg_ie', 15)->nullable();
            $table->string('im', 15)->nullable();
            $table->integer('cod_locale')->references('id')->on('locales');
            $table->bigInteger('telefone')->nullable();
            $table->bigInteger('celular')->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('tipo_consumidor', ['final', 'nao_final']);
            $table->enum('situacao_tributaria', ['nenhum', 'simples', 'lucro']);
            $table->enum('tipo_cliente', ['pf', 'pj']);
            $table->tinyInteger('cpf_consumidor_final')->default(0);
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
        Schema::dropIfExists('clients');
    }
}
