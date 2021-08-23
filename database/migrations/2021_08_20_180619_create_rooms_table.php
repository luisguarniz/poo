<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
           //  $table->id();
           $table->uuid('roomID')->primary();
           $table->unsignedBigInteger('idAdmin');
           $table->string('roomName');
           $table->string('roomCode');
           $table->boolean('isActive')->default('1');
        //   $table->string('codeBots')->nullable();
           $table->timestamps();
           

           

           $table->foreign('idAdmin')->references('id')->on('users');//se lee: sera llave foranea 'AdminUserCode'
                                                                                     // refenciado del campo 'AdminUserCode' de la tabla 'users'
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
