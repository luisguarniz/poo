<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //SE MIGRARON DE MANERA MANUAL CORRECTAMENTE POR QUE DA UN ERROR AL migrarlos con el comando migrate PARECER POR EL ORDEN EN QUE FUERON CREADAS
        Schema::create('status_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("idMesa");
            $table->unsignedBigInteger("idUser");
            $table->boolean('turn')->default("0");
            $table->timestamps();

            $table->foreign('idMesa')->references('id')->on('mesas');
            $table->foreign('idUser')->references('id')->on('users');// refenciado del campo 'id' de la tabla 'users'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_games');
    }
}
