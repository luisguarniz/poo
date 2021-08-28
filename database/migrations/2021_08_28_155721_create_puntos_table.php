<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePuntosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('puntos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("idMesa");
            $table->unsignedBigInteger("idUser");
            $table->integer("puntos_Cartas_Acumuladas")->default("0");
            $table->integer("puntos_Victorias")->default("0");
            $table->timestamps();

            $table->foreign('idMesa')->references('id')->on('mesas');
            $table->foreign('idUser')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('puntos');
    }
}
