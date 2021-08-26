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
        //SE MIGRARON DE MANERA MANUAL CORRECTAMENTE POR QUE DA UN ERROR AL migrarlos con el comando migrate PARECER POR EL ORDEN EN QUE FUERON CREADAS
        Schema::create('session_turns', function (Blueprint $table) {
            $table->uuid("idSessionTurn")->primary();
            $table->unsignedBigInteger("idUser")->nullable();//FK
            $table->uuid('idSessionGame')->nullable();//FK
            $table->boolean('turn')->default('0');
            $table->integer('orderTurn')->default('1');
            $table->timestamps();


            $table->foreign('idUser')->references('id')->on('users')
            ->onDelete('set null');

            $table->foreign('idSessionGame')
            ->references('idSessionGame')->on('session_games')
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
        Schema::dropIfExists('session_turns');
    }
}