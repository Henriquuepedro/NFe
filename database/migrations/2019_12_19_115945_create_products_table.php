<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 120);
            $table->string('reference', 255)->nullable();
            $table->string('bar_code', 255)->nullable();
            $table->string('unity', 15);
            $table->float('amount', 12, 2)->default(1.00);
            $table->tinyInteger('active')->default(1);
            $table->float('icms_1', 5,2)->default(0.00);
            $table->float('icms_2', 5,2)->default(0.00);
            $table->float('icms_3', 5,2)->default(0.00);
            $table->float('ipi_saida', 5,2)->default(0.00);
            $table->float('fcp', 5,2)->default(0.00);
            $table->float('lucro_pres', 5,2)->default(0.00);
            $table->float('inci_imposto', 5,2)->default(0.00);
            $table->float('imposto_impor', 5,2)->default(0.00);
            $table->string('ncm', 8)->nullable();
            $table->string('cest', 7)->nullable();
            $table->tinyInteger('subst_trib')->default(0);
            $table->tinyInteger('incid_pis_cofins')->default(0);
            $table->tinyInteger('indic_produc')->default(0);
            $table->tinyInteger('isento')->default(0);
            $table->tinyInteger('imune')->default(0);
            $table->tinyInteger('suspensao_icms')->default(0);
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
        Schema::dropIfExists('products');
    }
}
