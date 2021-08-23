<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();//agrege esta columna como llave primaria para poder usar la funcion "me" que funciona con el token.
            $table->uuid('adminUserCode');
            $table->string('nameUsuario');
            $table->string('customName')->nullable();//este campo se usara para almacenar el nombre modificado del participante
            $table->string('password');//columna necesaria para poder usar Auth::attempt($data)
            $table->boolean('isAdmin')->default('0');
            $table->boolean('isInvited')->default('0');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
