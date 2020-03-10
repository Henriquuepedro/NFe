<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablepriceproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tablepriceproducts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cod_table_price')->references('tableprices')->on('id')->onDelete('cascade');
            $table->integer('cod_product')->references('products')->on('id')->onDelete('cascade');
            $table->float('price', 12, 2)->default(0.00);
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
        Schema::dropIfExists('tablepriceproducts');
    }
}
