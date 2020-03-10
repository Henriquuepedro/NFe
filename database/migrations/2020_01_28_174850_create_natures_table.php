<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('natures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 256);
            $table->integer('cfop_state')->nullable();
            $table->integer('cfop_state_st')->nullable();
            $table->integer('cfop_no_state')->nullable();
            $table->integer('cfop_no_state_st')->nullable();
            $table->integer('customer_type');
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
        Schema::dropIfExists('natures');
    }
}
