<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_turns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("idUser");
            $table->unsignedBigInteger("idSessionGame");
            $table->boolean('turn')->default('0');
            $table->integer('orderTurn')->default('1');
            $table->boolean('enJuego')->default('1');//este campo sirve para el jugador que se desactiva durante el juego por no tener cartas que poner
            $table->timestamps();


            $table->foreign('idUser')->references('id')->on('users');
            $table->foreign('idSessionGame')->references('id')->on('session_games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_turns');
    }
}
