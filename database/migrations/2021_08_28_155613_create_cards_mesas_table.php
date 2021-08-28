<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsMesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards_mesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("idMesa");
            $table->integer("card_1_In_Mesa")->default("0");;
            $table->integer("card_2_In_Mesa")->default("0");;
            $table->integer("card_3_In_Mesa")->default("0");;
            $table->integer("card_4_In_Mesa")->default("0");;
            $table->integer("card_5_In_Mesa")->default("0");;
            $table->integer("card_6_In_Mesa")->default("0");;
            $table->integer("card_Otorongo_In_Mesa")->default("0");;
            $table->timestamps();

            $table->foreign('idMesa')->references('id')->on('mesas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards_mesas');
    }
}
