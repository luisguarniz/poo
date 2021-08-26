<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //SE MIGRARON DE MANERA MANUAL CORRECTAMENTE POR QUE DA UN ERROR AL migrarlos con el comando migrate PARECER POR EL ORDEN EN QUE FUERON CREADAS
        Schema::create('games', function (Blueprint $table) {
            $table->uuid("idGame")->primary();
            $table->uuid('idSessionGame')->nullable();//FK
            $table->uuid('idMesa')->nullable();//FK
            $table->uuid('idCard')->nullable();//FK
            $table->timestamps();

            $table->foreign('idSessionGame')
            ->references('idSessionGame')->on('session_games')
            ->onDelete('set null');

            $table->foreign('idMesa')
            ->references('idMesa')->on('mesas')
            ->onDelete('set null');
            
            $table->foreign('idCard')
            ->references('idCard')->on('cards')
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
        Schema::dropIfExists('games');
    }
}
