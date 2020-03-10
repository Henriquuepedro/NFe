<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FiscalNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiscal_notes', function (Blueprint $table) {
            $table->integer('cod_nf');
            $table->integer('cod_sale');
            $table->integer('cod_client');
            $table->string('document_client', 14);
            $table->string('rg_ie_client', 16);
            $table->string('uf_client', 2);
            $table->dateTime('date_emission');
            $table->dateTimeTz('seq');
            $table->string('name_nature', 60);
            $table->integer('cod_nature');
            $table->integer('finality');
            $table->float('qnty', 12, 2);
            $table->string('specie', 60);
            $table->float('gross_weight', 12, 2);
            $table->float('liquid_weight', 12, 2);
            $table->integer('shipping');
            $table->float('gross_value', 12, 2);
            $table->float('liquid_value', 12, 2);
            $table->string('return_sefaz', 255);
            $table->integer('status');
            $table->string('key', 44);
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
        Schema::dropIfExists('fiscal_notes');
    }
}
