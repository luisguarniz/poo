<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //SE MIGRARON DE MANERA MANUAL CORRECTAMENTE POR QUE DA UN ERROR AL migrarlos con el comando migrate PARECER POR EL ORDEN EN QUE FUERON CREADAS
        Schema::create('session_games', function (Blueprint $table) {
            $table->id();
            $table->uuid('roomID')->nullable();//FK
            $table->boolean('isActive')->default("1"); //empieza con 1 pero se cambia a cero cuando se crea otra session de juego
            $table->timestamps();

            $table->foreign('roomID')
            ->references('roomID')->on('rooms')
            ->onDelete('set null');     
        

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_games');
    }
}
