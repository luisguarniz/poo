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
            $table->uuid("idCardGame")->primary();
            $table->uuid("idMesa")->nullable();
            $table->unsignedBigInteger("idUser")->nullable();//FK
            $table->string("idCardInGame")->nullable();//este campo sera llenado despues que se llene la tabla games
            $table->timestamps();

            $table->foreign('idMesa')
            ->references('idMesa')->on('mesas')
            ->onDelete('set null');

            $table->foreign('idUser')->references('id')->on('users')
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
        Schema::dropIfExists('cards_games');
    }
}
