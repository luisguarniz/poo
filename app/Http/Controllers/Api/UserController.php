<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
  public $newUser;
  public $newInvited;

  public function makeUser(Request $request)
  {
   
    //Primero Creo un usuario
    $permitted_chars1 = '0123456789abcdefghijklmnopqrstuvwxyz';
    $nrorandom =  substr(str_shuffle($permitted_chars1), 0, 4); //guardamos los caracteres aleatorios

    //$character = Character::all()->random(); // con este metodo traigo un registro random
    //$nomCharacter = $character->characterName;
    $nomCharacter = $request->participanteNom;

    $this->newUser = new User();
    $this->newUser->adminUserCode = Uuid::uuid();
    $this->newUser->nameUsuario = $nomCharacter . "-" . $nrorandom;
    $this->newUser->customName = $nomCharacter;
    //$this->newUser->NameUsuario = $nomAnimal;
    $this->newUser->password = bcrypt('12345678');
    $this->newUser->isAdmin = '1';
    $this->newUser->save();

    //creamos un token que nos servira para la union de los usuarios
    $token = $this->newUser->CreateToken('authToken')->accessToken;



    return response()->json([
      'ok'  => true,
      'user' => $this->newUser,
      'token' => $token
    ]);
  }
  public function loginHost(Request $request)
  {
    $data = $request->only('nameUsuario', 'password');

    if (!Auth::attempt($data)) {
      return response()->json([
        'ok'   => false,
        'message' => 'error de credenciales',
      ]);
    }

    $token = Auth::user()->createToken('authToken')->accessToken;

    return response()->json([
      'ok' => true,
      'user' => Auth::user(),
      'token' => $token
    ]);
  }


  public function me()
  {
    return response()->json([
      'ok' => true,
      'user' => Auth::user()
    ]);
  }

  public function makeInvited(Request $request)
  {

    $permitted_chars1 = '0123456789abcdefghijklmnopqrstuvwxyz';
    $nrorandom =  substr(str_shuffle($permitted_chars1), 0, 4); //guardamos los caracteres aleatorios

    //$character = Character::all()->random(); // con este metodo traigo un registro random
    //$nomCharacter = $character->characterName;
    $nomCharacter = $request->participanteNom;

    $this->newInvited = new User();
    $this->newInvited->adminUserCode = Uuid::uuid();
    $this->newInvited->nameUsuario = $nomCharacter . "-" . $nrorandom;
    $this->newInvited->customName = $nomCharacter;
    //$this->newInvited->NameUsuario = $nomAnimal;
    $this->newInvited->password = bcrypt('12345678');
    $this->newInvited->isInvited = '1';
    $this->newInvited->save();


    //creamos un token que nos servira para la union de los usuarios
    //necesario para unirse a los canales privados
    $token = $this->newInvited->CreateToken('authToken')->accessToken;


    return response()->json([
      'ok'  => true,
      'user' => $this->newInvited,
      'token' => $token
    ]);
  }

  public function editNameUser(Request $request)
  {

    $namepokemon = User::select('users.customName')
      ->where('users.adminUserCode', $request->adminUserCode)->first();

    //reseteo el nombre para que no se concatene si editan mas de una ves su nombre
    $namepokemonReset = Str::after($namepokemon->customName, "-");

    $newName = User::where('users.AdminUserCode', $request->adminUserCode)
      ->update([
        'CustomName' => $request->customName . "-" . $namepokemonReset
      ]);

    return response()->json([
      'messagge' => "se modifico el nombre"
    ]);
  }

  public function isAdmin(Request $request)
  {

    $query = DB::table('users')
      ->select('users.isAdmin')
      ->where('users.id', $request->id)
      ->first();
    //   return $query;
    return response()->json([
      'isAdmin' => $query
    ]);
  }

  public function getAdmin(Request $request)
  {

    $query = DB::table('rooms')
      ->select('rooms.idAdmin')
      ->where('rooms.roomID', $request->roomID)
      ->first();
    //   return $query;
    return response()->json([
      'idAdmin' => $query
    ]);
  }

  public function getUserTurn(Request $request)
  {

    $customName = DB::table('users')
      ->select('users.customName')
      ->where('users.id', $request->id)
      ->first();
    //   return $query;
    return response()->json([
      'customName' => $customName
    ]);
  }

}

