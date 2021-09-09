<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards_user;
use App\Models\Punto;
use Illuminate\Http\Request;

class PuntosController extends Controller
{

    public function makePoints(Request $request){

        for ($i=0; $i < count($request->idsParticipantes); $i++) {
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

    public function updatePoints(Request $request){

    $registro = Punto::where('idMesa',$request->idMesa)
     ->where('idUser',$request->idUser)->first();

     $puntos = $registro->puntos_Cartas_Acumuladas + $request->puntos;

     if ($puntos > 39) {

     $puntosVictorias = Punto::where('idMesa',$request->idMesa)
      ->where('idUser',$request->idGanador)->first();

      Punto::where('idMesa',$request->idMesa)
      ->where('idUser',$request->idGanador)
      ->update([
        'puntos_Victorias'=> $puntosVictorias->puntos_Victorias + 1
      ]); 
      $participantes = Punto::where('idMesa',$request->idMesa)->orderBy('puntos_Cartas_Acumuladas')->get();
      return response()->json([
        '$participantes' => $participantes
      ]);
     }
     else
     {
      Punto::where('idMesa',$request->idMesa)
      ->where('idUser',$request->idUser)
      ->update([
        'puntos_Cartas_Acumuladas'=> $puntos
      ]);

      return response()->json([
        'mensaje' => "se Actualizo los puntos de las cartas acumuladas"
      ]);

     }

      
    }

    public function getSumPoints(Request $request){

        $loser = Cards_user::where('idMesa', $request->idMesa)
       ->where('idUser','!=',$request->idUser)
       ->first();
    
        $userPerdedor = Punto::where('idMesa', $request->idMesa)
         ->where('idUser', $request->idUser)
         ->where('puntos_Cartas_Acumulados','>',39)
         ->first();
       
     
       return response()->json([
        '$userPerdedor' => $userPerdedor
      ]);
      }
}
