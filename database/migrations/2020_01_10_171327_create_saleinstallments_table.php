<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleinstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saleinstallments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cod_sale')->references('id')->on('sales')->onDelete('cascade');
            $table->integer('installment_number');
            $table->integer('due_day');
            $table->date('due_date');
            $table->float('value', 12, 2)->default(0.00);
            $table->date('pay_day')->nullable();
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
        Schema::dropIfExists('saleinstallments');
    }
}
