<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards_game;
use Illuminate\Http\Request;

class Card_GameController extends Controller
{
    //esto me dara la mitad del maso
    public function makeCardsGame(Request $request)
    {

        $nroDeUsuarios = count($request->Participantes);
        $nroDeBarajas = 0;
        $cartasXtipoXBaraja = 8;//numero fijo para hacer un total de 56 cartas al multiplicar por tiposCartas
        $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
        $tiposCartas = 7;//(1,2,3,4,5,6,Otorongo)
        $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
        $cartasAjugar = $Mazo / 2;
        $Sumvalor = 0;
      //  $nroRandom = 0;

        //definimos el numero de barajas segun el numero de participantes
       if ($nroDeUsuarios < 4) {

         $nroDeBarajas = 1; 
         $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
         $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
         $cartasAjugar = $Mazo / 2;       
       }elseif($nroDeUsuarios < 7) {
        return response()->json([
          'message' => "entro al if - de 7",
          'nroDeUsuarios'=> $nroDeUsuarios
        ]);
        $nroDeBarajas = 2;
        $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
        $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
        $cartasAjugar = $Mazo / 2;
       }
      elseif($nroDeUsuarios < 10) {
        return response()->json([
          'message' => "entro al if - de 10",
          'nroDeUsuarios'=> $nroDeUsuarios
        ]);
        $nroDeBarajas = 3; 
        $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
        $Mazo = $nroCartasXtipoXmazo * $tiposCartas; 
        $cartasAjugar = $Mazo / 2;        
      }
      

      //llenamos cantidad de cartas por tipo de carta(1,2,3,4,5,6,Otorongo)
      for ($i=1; $i < 8; $i++) { 

        if ($i == 7) {
          $valor = $Sumvalor - $cartasAjugar;
        }

        $nroRandom = mt_rand(0,$cartasAjugar);
         $card = new Cards_game();
         $card->idMesa = $request->idMesa;
         $card->idCard = $i; // aprovechamos el contador ya que empieza en 1 como el id de la primera carta en la tabla Cards
         $nroRandom = mt_rand(0,$cartasAjugar);
         $dividirCosiente = intval($nroRandom / $nroCartasXtipoXmazo);
         $valor  = $nroCartasXtipoXmazo * $dividirCosiente;
         $valor = $nroRandom - $valor;
         $card->cardStock = $valor;
         $cartasAjugar = $cartasAjugar - $valor; //nuevo valor cartasAjugar para llenar la proxima carta
         $Sumvalor = $Sumvalor + $valor;
         $card->save();
      }

      return response()->json([
        'message' => "se registraron las cartas a jugar"
      ]);

      return response()->json([
        'message' => "entro al if - de 7",
        'nroDeUsuarios'=> $nroDeUsuarios,
        '$nroCartasXtipoXmazo'=> $nroCartasXtipoXmazo,
        '$Mazo'=> $Mazo,
        'cartasAjugar'=> $cartasAjugar
      ]);
    }
}
