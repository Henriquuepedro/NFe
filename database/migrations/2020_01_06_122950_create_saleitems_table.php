<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saleitems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cod_sale')->references('id')->on('sales')->onDelete('cascade');
            $table->integer('cod_product')->references('id')->on('products');
            $table->float('qnty_iten', 12, 2)->default(0.00);
            $table->float('value_iten', 12, 2)->default(0.00);
            $table->float('icms_st_iten', 12, 2)->default(0.00);
            $table->float('base_icms_st_iten', 12, 2)->default(0.00);
            $table->float('icms_iten', 12, 2)->default(0.00);
            $table->float('base_icms_iten', 12, 2)->default(0.00);
            $table->float('st_iten', 12, 2)->default(0.00);
            $table->float('icms_perc_iten', 5, 2)->default(0.00);
            $table->float('ipi_iten', 12, 2)->default(0.00);
            $table->float('ipi_perc_iten', 5, 2)->default(0.00);
            $table->float('discount_iten', 12, 2)->default(0.00);
            $table->tinyInteger('have_st_iten')->default(0);
            $table->float('value_total_iten', 12, 2)->default(0.00);
            $table->string('complement_product_iten');
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
        Schema::dropIfExists('saleitems');
    }
}
