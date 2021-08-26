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

//creando bots
/* 
  public function makeBots(Request $request)
  {
    $nroParticipantes = intval($request->nroParticipantes);
    $nombres = array();
    $listParticipantes = array();

    if ($nroParticipantes < 11) {

      for ($i = 0; $i < $nroParticipantes; $i++) {
        //Primero Creo un usuario
        $permitted_chars1 = '0123456789abcdefghijklmnopqrstuvwxyz';
        $nrorandom =  substr(str_shuffle($permitted_chars1), 0, 4); //guardamos los caracteres aleatorios

        $character = Character::all()->random(); // con este metodo traigo un registro random
        $nomCharacter = $character->characterName;
        $nombreUnico  = $nomCharacter . "-" . $nrorandom;

        $this->newUser = new User();
        $this->newUser->adminUserCode = Uuid::uuid();
        $this->newUser->nameUsuario = $nomCharacter . "-" . $nrorandom;
        $this->newUser->customName = $nomCharacter;
        //$this->newUser->NameUsuario = $nomAnimal;
        $this->newUser->password = bcrypt('12345678');
        $this->newUser->isAdmin = '1';
        $this->newUser->save();

        $nombres[$i] = $nombreUnico;
      }
    }else{
      return response()->json([
        'mensaje' => "Tiene que ser menor a 11 participantes"
      ]);
    }

    for ($i=0; $i < $nroParticipantes ; $i++) { 
      $listParticipantes[$i] = DB::table('users')
      ->select('*')
      ->where('nameUsuario', $nombres[$i])
      ->first();
    }
    return response()->json([
      'nroParticipantes' => $listParticipantes
    ]);

  }
*/
public function makeUsers(Request $request)
{

  $nroParticipantes = count($request->Participantes);
  $nombres = array();
  $listParticipantes = array();

  if ($nroParticipantes < 11) {

    for ($i = 0; $i < $nroParticipantes; $i++) {
      //Primero Creo un usuario
      $permitted_chars1 = '0123456789abcdefghijklmnopqrstuvwxyz';
      $nrorandom =  substr(str_shuffle($permitted_chars1), 0, 4); //guardamos los caracteres aleatorios

      $nomCharacter = $request->Participantes[$i]["participante"];
      $nombreUnico  = $nomCharacter . "-" . $nrorandom;

      $this->newUser = new User();
      $this->newUser->adminUserCode = Uuid::uuid();
      $this->newUser->nameUsuario = $nombreUnico;
      $this->newUser->customName = $nomCharacter;
      $this->newUser->password = bcrypt('12345678');
      $this->newUser->isAdmin = '1';
      $this->newUser->save();
      $nombres[$i] = $nombreUnico;
    }
  }else{
    return response()->json([
      'mensaje' => "Tiene que ser menor a 11 participantes"
    ]);
  }

  for ($i=0; $i < $nroParticipantes ; $i++) { 
    $listParticipantes[$i] = DB::table('users')
    ->select('*')
    ->where('nameUsuario', $nombres[$i])
    ->first();
  }
  return response()->json([
    'nroParticipantes' => $listParticipantes
  ]);

}

  // crear metodo que traiga los usuarios que se crearon en el modo solo
  //para eso se consultara la tabla rooms haciendo un where con codeBots como coincidencia

  public function getUsersSolo(Request $request){

    $userList = DB::table('rooms')
    ->join('users', 'users.id', '=', 'rooms.idAdmin')
    ->select('*')
    ->where('rooms.codeBots', $request->codeBots)
    ->get();
  //   return $query;
  return response()->json([
    'userList' => $userList
  ]);
  }
}

