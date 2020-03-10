<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('razao_social', 256);
            $table->string('fantasia', 256);
            $table->string('cnpj', 14);
            $table->string('telephone', 11);
            $table->string('ie', 16);
            $table->string('iest', 16);
            $table->string('im', 16);
            $table->string('cnae', 16);
            $table->integer('number_start_nfe');
            $table->integer('regime_trib');
            $table->string('logotipo', 256);
            $table->string('path_certificado', 256);
            $table->string('pass_certificado', 256);
            $table->string('cep', 8);
            $table->string('place', 256);
            $table->string('number', 256);
            $table->string('complement', 256);
            $table->string('district', 256);
            $table->string('cod_ibge_city', 256);
            $table->integer('cod_user_reg')->references('id')->on('users')->onDelete('cascade');
            $table->integer('cod_user_alt')->references('id')->on('users')->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
