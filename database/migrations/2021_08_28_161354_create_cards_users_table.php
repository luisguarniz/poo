<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("idMesa");
            $table->unsignedBigInteger("idUser");
            $table->integer("card_1_In_Game")->default("0");;
            $table->integer("card_2_In_Game")->default("0");;
            $table->integer("card_3_In_Game")->default("0");;
            $table->integer("card_4_In_Game")->default("0");;
            $table->integer("card_5_In_Game")->default("0");;
            $table->integer("card_6_In_Game")->default("0");;
            $table->integer("card_Otorongo_In_Game")->default("0");;
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
        Schema::dropIfExists('cards_users');
    }
}
