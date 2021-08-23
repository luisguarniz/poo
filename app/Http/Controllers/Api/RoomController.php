<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Room;
use Faker\Provider\Uuid;

class RoomController extends Controller
{
       //este metodo espera el codigo y el token que devuelve la tabla user
  public function makeRoom(Request $request)
  {

    $ciudad = City::all()->random(); // con este metodo traigo un registro random
    $nomCiudad = $ciudad->cityName;
    $permitted_chars2 = '0123456789';
    $roomName = $nomCiudad . '-' . substr(str_shuffle($permitted_chars2), 0, 4);


    $permitted_chars3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $roomCodeLetras = substr(str_shuffle($permitted_chars3), 0, 3);
    $permitted_chars4 = '0123456789';
    $roomCodeNumeros = substr(str_shuffle($permitted_chars4), 0, 3); //guardamos los caracteres aleatorios


    $newRoom = new Room;
    $newRoom->roomID = Uuid::uuid();
    $newRoom->idAdmin = $request->idAdmin; // es lo que traigo en los parametros de la funcion
    $newRoom->roomName = $roomName;
    $newRoom->roomCode = $roomCodeLetras . $roomCodeNumeros;
    $newRoom->save();


    //una ves creada la sala con save() pasamos a retornar un objeto a la vista
    $response['id'] = $newRoom->id;
    $response['roomID'] = $newRoom->roomID;
    $response['roomName'] = $newRoom->roomName;
    $response['roomCode'] = $newRoom->roomCode;
    $response['idAdmin'] = $newRoom->idAdmin;
    return $response;
  }


  public function desactivateRoom(Request $request)
  {

    $room = Room::where('idAdmin', $request->idAdmin)->update([
      'IsActive' => '0'
    ]);
  }

  public function getRoomInvited(Request $request)
  {
    $message = null;
    $room = Room::where('RoomCode',$request->roomCode)->first();

    if ($room == null) {
      return $message;
    }
    return response()->json([
      'roomID' => $room->roomID,
      'roomNameI' => $room->roomName,
      'roomCodeI' => $room->roomCode
    ]);
  }

  public function getRoomhost(Request $request)
  {
    $message = null;
    $room = Room::where('roomCode',$request->roomCode)->first();

    if ($room == null) {
      return $message;
    }
    return response()->json([

      'roomID'=>$room->roomID,
      'roomName' => $room->roomName,
      'roomCode' => $room->roomCode,
      'idAdmin'=> $room->idAdmin
    ]);
  }

  //metodo para jugar solo
  public function makeRoomSolo(Request $request)
  {

    $arregloidParticipantes = explode(",", $request->idParticipantes);

    //se necesita crear tantas salas como usuarios y adicionarle un codigo unico a todos para consultar al grupo de usuarios

    $ciudad = City::all()->random(); // con este metodo traigo un registro random
    $nomCiudad = $ciudad->cityName;
    $permitted_chars2 = '0123456789';
    $roomName = $nomCiudad . '-' . substr(str_shuffle($permitted_chars2), 0, 4);


    $permitted_chars3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $roomCodeLetras = substr(str_shuffle($permitted_chars3), 0, 3);
    $permitted_chars4 = '0123456789';
    $roomCodeNumeros = substr(str_shuffle($permitted_chars4), 0, 3); //guardamos los caracteres aleatorios

    $permitted_chars3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $roomcodeBots= substr(str_shuffle($permitted_chars3), 0, 8);

    for ($i=0; $i <count($arregloidParticipantes); $i++) { 
      $newRoom = new Room;
      $newRoom->roomID =   Uuid::uuid();
      $newRoom->idAdmin =  $arregloidParticipantes[$i]; // es lo que traigo en los parametros de la funcion
      $newRoom->roomName = $roomName;
      $newRoom->roomCode = $roomCodeLetras . $roomCodeNumeros;
      $newRoom->codeBots = $roomcodeBots;
      $newRoom->save();
    }
    return response()->json([
      'roomID'=> $newRoom->roomID,
      'codeBots' => $newRoom->codeBots
    ]);
  }
}
