<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards_user;
use App\Models\Punto;
use Illuminate\Http\Request;
use stdClass;

class PuntosController extends Controller
{

  public function makePoints(Request $request)
  {

    for ($i = 0; $i < count($request->idsParticipantes); $i++) {
      $puntos = new Punto;
      $puntos["idMesa"] = $request->idMesa;
      $puntos["idUser"] = $request->idsParticipantes[$i];
      $puntos["puntos_Cartas_Acumuladas"] = 0;
      $puntos["puntos_Victorias"] = 0;
      $puntos->save();
    }
    return response()->json([
      '$userPerdedor' => "se inicializaron los puntos para los participantes"
    ]);
  }

  // puntos de cartas acumuladas
  public function updatePoints(Request $request)
  {

    $registro = Punto::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)->first();

    $puntos = $registro->puntos_Cartas_Acumuladas + $request->puntos;

    Punto::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)
      ->update([
        'puntos_Cartas_Acumuladas' => $puntos
      ]);

    return response()->json([
      'puntos' => $puntos
    ]);
  }
  public function updatePointsWin(Request $request)
  {

    $puntosVictorias = Punto::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)->first();

    Punto::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)
      ->update([
        'puntos_Victorias' => $puntosVictorias->puntos_Victorias + 1
      ]);

    $participantes = Punto::where('idMesa', $request->idMesa)->orderBy('puntos_Cartas_Acumuladas')->get();
    return response()->json([
      '$participantes' => $participantes
    ]);
  }
  public function updatePointsAll(Request $request)
  {
    $puntosXuser = array();
    $cardsUser = Cards_user::where("idMesa", $request->idMesa)->get();

    for ($i = 0; $i < count($cardsUser); $i++) {
      $user = new stdClass(); //idUser y total de puntos
      $card_1_In_Game = $cardsUser[$i]->card_1_In_Game;
      $card_2_In_Game = $cardsUser[$i]->card_2_In_Game;
      $card_3_In_Game = $cardsUser[$i]->card_3_In_Game;
      $card_4_In_Game = $cardsUser[$i]->card_4_In_Game;
      $card_5_In_Game = $cardsUser[$i]->card_5_In_Game;
      $card_6_In_Game = $cardsUser[$i]->card_6_In_Game;
      $card_Otorongo_In_Game = $cardsUser[$i]->card_Otorongo_In_Game;

      $card_1_In_Game = $card_1_In_Game * 1;
      $card_2_In_Game = $card_2_In_Game * 2;
      $card_3_In_Game = $card_3_In_Game * 3;
      $card_4_In_Game = $card_4_In_Game * 4;
      $card_5_In_Game = $card_5_In_Game * 5;
      $card_6_In_Game = $card_6_In_Game * 6;
      $card_Otorongo_In_Game = $card_Otorongo_In_Game * 10;

      $user->idUser = $cardsUser[$i]->idUser;
      $user->puntosCartasAcumuladas = $card_1_In_Game + $card_2_In_Game + $card_3_In_Game + $card_4_In_Game + $card_5_In_Game + $card_6_In_Game + $card_Otorongo_In_Game;
     $puntosUsuario = Punto::where("idMesa",$request->idMesa)
      ->where("idUser",$user->idUser)->first();

      $puntosTotal = $puntosUsuario->puntos_Cartas_Acumuladas + $user->puntosCartasAcumuladas;

      Punto::where("idMesa",$request->idMesa)
      ->where("idUser",$user->idUser)
      ->update([
        'puntos_Cartas_Acumuladas' => $puntosTotal
      ]);
    
      array_push($puntosXuser, $puntosTotal);
    }

    for ($i=0; $i <count($puntosXuser) ; $i++) { 
      //basta que un participante pase los 39 puntos le sumamos un punto al participante con menor puntuacion y devolvemos el idUser
      if ($puntosXuser[$i] > 39) {
       $user =  Punto::where("idMesa",$request->idMesa)->orderBy('puntos_Cartas_Acumuladas', 'asc')->get();

       $puntos = $user[0]->puntos_Victorias + 1;

       Punto::where("idMesa",$request->idMesa)
       ->where("idUser",$user[0]->idUser)
       ->update([
         'puntos_Victorias' => $puntos
       ]);

        return response()->json([
          'idUserGanador' => $user[0]->idUser
        ]);
      }
    }
    //si sale del for quiere decir que ningun participante paso los 39 puntos
    return response()->json([
      'mensaje' => "aun no llegan a mas de 39 puntos, no hay un ganador"
    ]);
  }

  public function getSumPoints(Request $request)
  {

    $loser = Cards_user::where('idMesa', $request->idMesa)
      ->where('idUser', '!=', $request->idUser)
      ->first();

    $userPerdedor = Punto::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)
      ->where('puntos_Cartas_Acumulados', '>', 39)
      ->first();


    return response()->json([
      '$userPerdedor' => $userPerdedor
    ]);
  }

  public function resetPoints(Request $request)
  {

    Punto::where('idMesa', $request->idMesa)
      ->update([
        'puntos_Cartas_Acumuladas' => 0
      ]);
  }
}
