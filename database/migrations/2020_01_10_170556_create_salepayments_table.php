<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salepayments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cod_sale')->references('id')->on('sales')->onDelete('cascade');
            $table->float('shipping', 12, 2)->default(0.00);
            $table->float('insurance', 12, 2)->default(0.00);
            $table->float('other_expense', 12, 2)->default(0.00);
            $table->float('daily_charges', 12, 2)->default(0.00);
            $table->float('icms_st', 12, 2)->default(0.00);
            $table->float('base_icms_st', 12, 2)->default(0.00);
            $table->float('icms', 12, 2)->default(0.00);
            $table->float('base_icms', 12, 2)->default(0.00);
            $table->float('ipi', 12, 2)->default(0.00);
            $table->float('discount', 12, 2)->default(0.00);
            $table->float('gross_value', 12, 2)->default(0.00);
            $table->float('liquid_value', 12, 2)->default(0.00);
            $table->integer('quantity_installment');
            $table->integer('calculate_automatic');
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
        Schema::dropIfExists('salepayments');
    }
}
