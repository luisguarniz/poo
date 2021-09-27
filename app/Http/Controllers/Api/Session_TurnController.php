<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session_turn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Session_TurnController extends Controller
{
    public function makeSessionTurn(Request $request){
         
      //creamos y asignamos un numero de posicion para cada jugador
      //no quiere decir que el jugador con posicion uno va ser el primero en 
      //esto quiere decir que el admin tendra el numero menor siempre pero no sera el primero en jugar siempre
        $alluser = array();
        $tunrInicial = false;
        $orderTurnInicial = 1;
        $alluser = $request->idsParticipantes;
        for ($i=0; $i < count($alluser); $i++) { 
        $sessionTurn = new Session_turn;
        //$sessionTurn["idTurn"] = Uuid::uuid();
        $sessionTurn["idUser"] = $alluser[$i];
        $sessionTurn["idSessionGame"] = $request->idSessionGame;
        $sessionTurn["turn"] = $tunrInicial;
        $sessionTurn["orderTurn"] = $i + 1;
        $sessionTurn->save();
        }
        
        //elejimos un numero de los jugadores para que empiece a jugar
        //luego seguira el numero siguiente en jugar
        $idPrimerJugador = Session_turn::where('session_turns.idSessionGame', $request->idSessionGame)->get()->random();


        Session_turn::where('session_turns.idUser', $idPrimerJugador->idUser)
        ->update([
          'turn' => true,
        ]);
        return response()->json([
            'first' => $idPrimerJugador->idUser
          ]);
    }
/* 
    public function changeTurn(Request $request){
      //cambio el estado del turn del idUser enviado a 0
       Session_turn::where('session_turns.idUser', $request->idUser)
       ->update([
        'turn' => false,
      ]);

      //obtengo el orderTurn del idUser enviado
      $orderTurn = DB::table('session_turns')
      ->select('session_turns.orderTurn')
      ->where('session_turns.idUser', $request->idUser)
      ->first();
  
      //consulto el siguiente jugador en turno y almaceno en orderTurnActual
      $orderTurnActual = Session_turn::where('session_turns.idSessionGame', $request->idSessionGame)
      ->where('session_turns.orderTurn', $orderTurn->orderTurn + 1)
      ->first();

      //pregunto si existe o no este turno ya que si me devuelve null entonces estaria tocandole al que tiene el turno nro 1
      if ($orderTurnActual == null) {
       Session_turn::where('session_turns.idSessionGame', $request->idSessionGame)
       ->where('session_turns.orderTurn', 1)
       ->update([
        'turn' => true,
      ]);

      $nextTurn = DB::table('session_turns')
      ->select('session_turns.idUser')
      ->where('session_turns.idSessionGame', $request->idSessionGame)
      ->where('session_turns.orderTurn', 1)
      ->first();

      
     $customName = DB::table('users')
     ->select('users.customName')
     ->where('users.id', $nextTurn->idUser)
     ->first();

      return response()->json([
        'nextTurn' => $nextTurn,
        'customName'=> $customName
      ]);

      }
      else{
        
        Session_turn::where('session_turns.idSessionGame', $request->idSessionGame)
        ->where('session_turns.orderTurn', $orderTurnActual->orderTurn)
        ->update([
         'turn' => true,
       ]);
        
        $nextTurn = DB::table('session_turns')
      ->select('session_turns.idUser')
      ->where('session_turns.idSessionGame', $request->idSessionGame)
      ->where('session_turns.orderTurn', $orderTurnActual->orderTurn)
      ->first();
      }

      $customName = DB::table('users')
      ->select('users.customName')
      ->where('users.id', $nextTurn->idUser)
      ->first();
 
       return response()->json([
         'nextTurn' => $nextTurn,
         'customName'=> $customName
       ]);
 
    }
*/
    public function quitarEnjuego(Request $request){
      Session_turn::where("idSessionGame",$request->idSessionGame)
      ->where("idUser",$request->idUser)
      ->update([
       'enJuego' => 0
     ]);

     return response()->json([
      'mensaje' => "se te quito el turno"
    ]);
 }
 public function changeTurn(Request $request){

  //cambio el estado del turn del idUser enviado a 0
  Session_turn::where('session_turns.idUser', $request->idUser)
  ->update([
   'turn' => false,
 ]);

  $idsParticipantes = array();
  $idsEnJuego = Session_turn::select('idUser')
  ->where("idSessionGame",$request->idSessionGame)
  ->where("enJuego", 1)
  ->orderBy('idUser', 'asc')
  ->get();
  $arrayLength = count($idsEnJuego);

  if ($arrayLength == 0) {

    //devuelvo el 0 cuando todos estan eliminados
    return response()->json([
      'nextTurn' => $arrayLength
    ]);
  
  }
 
  for ($i=0; $i < $arrayLength; $i++) { 
    array_push($idsParticipantes,$idsEnJuego[$i]->idUser);
  }

  //tengo la posicion del idUser que cambiara
  $posicion = array_search($request->idUser, $idsParticipantes);
  $posicion = $posicion + 1;

  if ($posicion == $arrayLength) {
    $leToca = $idsParticipantes[0];
    Session_turn::where("idSessionGame",$request->idSessionGame)
    ->where("idUser",$leToca)
    ->update([
      'turn' => true
    ]);

    $customName = DB::table('users')
    ->select('users.customName')
    ->where('users.id', $leToca)
    ->first();

     return response()->json([
       'nextTurn' => $leToca,
       'customName'=> $customName
     ]);

  }
  else{
    $leToca = $idsParticipantes[$posicion];
    Session_turn::where("idSessionGame",$request->idSessionGame)
    ->where("idUser",$leToca)
    ->update([
      'turn' => true
    ]);

    $customName = DB::table('users')
      ->select('users.customName')
      ->where('users.id', $leToca)
      ->first();
 
       return response()->json([
         'nextTurn' => $leToca,
         'customName'=> $customName
       ]);
  }
 }

    public function getTurn(Request $request){
      
       $inTurn = Session_turn::where('idSessionGame', $request->idSessionGame)
       ->where('turn', true)->first();

       return response()->json([
        'turn' => $inTurn
      ]);
    }


    public function getUserTurnNow(Request $request)
    {
      $customName = DB::table('session_turns')
      ->join('users', 'users.id', '=', 'session_turns.idUser')
      ->select('users.customName')
      ->where('session_turns.idSessionGame', $request->idSessionGame)
      ->where('session_turns.turn', 1)
      ->first();

      return response()->json([
        'customName' => $customName
      ]);

    }

    public function resetearTurnos(Request $request){

      Session_turn::where('idSessionGame', $request->idSessionGame)
        ->update([
          'turn' => false,
          'enJuego' => 1
        ]);
        //elejimos un numero de los jugadores para que empiece a jugar
        //luego seguira el numero siguiente en jugar
        $idPrimerJugador = Session_turn::where('session_turns.idSessionGame', $request->idSessionGame)->get()->random();


        Session_turn::where('session_turns.idUser', $idPrimerJugador->idUser)
        ->update([
          'turn' => true,
        ]);
        return response()->json([
            'first' => $idPrimerJugador->idUser
          ]);
    }

}
