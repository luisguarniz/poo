<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //SE MIGRARON DE MANERA MANUAL CORRECTAMENTE POR QUE DA UN ERROR AL migrarlos con el comando migrate PARECER POR EL ORDEN EN QUE FUERON CREADAS
        Schema::create('cards_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idMesa');
            $table->unsignedBigInteger('idCard');
            $table->integer("cardStock")->default("0");
            $table->timestamps();

            $table->foreign('idMesa')->references('id')->on('mesas');
            $table->foreign('idCard')->references('id')->on('cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards_games');
    }
}
