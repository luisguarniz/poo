<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards_game;
use App\Models\Cards_mesa;
use App\Models\Cards_user;
use App\Models\Mesa;
use App\Models\Punto;
use Illuminate\Http\Request;
use stdClass;

class Card_GameController extends Controller
{
  //esto me dara la mitad del maso
  public function makeCardsGame(Request $request)
  {

    $nroDeUsuarios = count($request->Participantes);
    $nroDeBarajas = 0;
    $cartasXtipoXBaraja = 8; //numero fijo para hacer un total de 56 cartas al multiplicar por tiposCartas
    $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
    $tiposCartas = 7; //(1,2,3,4,5,6,Otorongo)
    $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
    $cartasAjugar = $Mazo / 2;
    $valor = 0;
    $Sumvalor = 0;
    $cartasAjugarmenosStock = 0;
    //  $nroRandom = 0;

    //definimos el numero de barajas segun el numero de participantes
    if ($nroDeUsuarios < 4) {

      $nroDeBarajas = 1;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    } elseif ($nroDeUsuarios < 7) {
      
      $nroDeBarajas = 2;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    } elseif ($nroDeUsuarios < 11) {
      
      $nroDeBarajas = 3;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    }


    //llenamos cantidad de cartas por tipo de carta(1,2,3,4,5,6,Otorongo)
    for ($i = 1; $i < 8; $i++) {

      if ($i == 7) {
        $valor = $cartasAjugar - $Sumvalor;

        $nroRandom = mt_rand(0, $cartasAjugarmenosStock);
        $card = new Cards_game();
        $card->idMesa = $request->idMesa;
        $card->idCard = $i; // aprovechamos el contador ya que empieza en 1 como el id de la primera carta en la tabla Cards
        $dividirCosiente = intval($nroRandom / $nroCartasXtipoXmazo);
        $card->cardStock = $valor;
        $cartasAjugarmenosStock = $cartasAjugarmenosStock - $valor; //nuevo valor cartasAjugar para llenar la proxima carta
        $Sumvalor = $Sumvalor + $valor;
        $card->save();

        $cards = Cards_game::where('idMesa', $request->idMesa)->get();
        return response()->json([
          'cartasMazo' => "se crearon las cartas"
        ]);
      }

      $nroRandom = mt_rand(0, $cartasAjugarmenosStock);
      $card = new Cards_game();
      $card->idMesa = $request->idMesa;
      $card->idCard = $i; // aprovechamos el contador ya que empieza en 1 como el id de la primera carta en la tabla Cards
      $dividirCosiente = intval($nroRandom / $nroCartasXtipoXmazo);
      $valor  = $nroCartasXtipoXmazo * $dividirCosiente;
      $valor = $nroRandom - $valor;
      $card->cardStock = $valor;
      $cartasAjugarmenosStock = $cartasAjugarmenosStock - $valor; //nuevo valor cartasAjugar para llenar la proxima carta
      $Sumvalor = $Sumvalor + $valor;
      $card->save();
    }
  }

  //este metodo tambien asigna una carta a la mesa
  public function makeCardsUser(Request $request)
  {

    $cantidadCartasXUsuario = 6;
    $cantidadxCarta = []; //las cartas que se le descontara al stock
    $stockxCarta = [];
    $nuevoStock = [];
    $nroDeUsuarios = count($request->Participantes);

    //esta consulta siempre tiene que traer solo 7 cartas SIEMPRE
    //por eso cada vez que un jugador llega a 40 puntos se crea una nueva mesa


    for ($ili = 0; $ili < $nroDeUsuarios; $ili++) {

      //1.-se consulta la tabla cards_game segun el idMesa y se obtiene un arreglo de 7 registros
      $cards = Cards_game::where('idMesa', $request->idMesa)->get();

      $cantidadCartas = count($cards);

      //2.-se crean los registros en la tabla cards_users apartir de la variable $cards


      for ($i = 0; $i < $cantidadCartas; $i++) {

        //llenamos un array para con el stock actual de las cartas en cards_game
        array_push($stockxCarta, $cards[$i]->cardStock);

        $nroRandom = mt_rand(0, $cantidadCartasXUsuario);

        //comprobamos que la carta tenga stock para repartir al jugador
        if ($cards[$i]->cardStock == 0) {


          $valor = 0;

          //le digo que al otorongo siempre le de las cartas que sobraron
          if ($i == 6) {
            $valor = $cantidadCartasXUsuario;
          }
        } else {
          $dividirCosiente = intval($nroRandom / $cards[$i]->cardStock);
          $valor =  $cards[$i]->cardStock * $dividirCosiente;
          $valor = $nroRandom - $valor;

          //le digo que al otorongo siempre le de las cartas que sobraron
          if ($i == 6) {
            $valor = $cantidadCartasXUsuario;
          }
        }

        //agregamos a un array la cantidad de cartas para descontarlo del stock al final
        array_push($cantidadxCarta, $valor);
        $cantidadCartasXUsuario = $cantidadCartasXUsuario - $valor;
      }

      for ($k = 0; $k < count($cantidadxCarta); $k++) {
        $nuevovalor = abs($stockxCarta[$k] - $cantidadxCarta[$k]); // con abs eliminamos los valores negativos que se obtienen al restar 0 - 2 por ejemplo
        array_push($nuevoStock, $nuevovalor);
      }

      //le doy los nuevos valores al stock de cada carta para el siguiente jugador
      $stockxCarta = $nuevoStock;

      //creo un registro para un participante
      $cardsUser = new Cards_user();
      $cardsUser['idMesa'] = $request->idMesa;
      $cardsUser['idUser'] = $request->Participantes[$ili];
      $cardsUser['card_1_In_Game'] = $cantidadxCarta[0];
      $cardsUser['card_2_In_Game'] = $cantidadxCarta[1];
      $cardsUser['card_3_In_Game'] = $cantidadxCarta[2];
      $cardsUser['card_4_In_Game'] = $cantidadxCarta[3];
      $cardsUser['card_5_In_Game'] = $cantidadxCarta[4];
      $cardsUser['card_6_In_Game'] = $cantidadxCarta[5];
      $cardsUser['card_Otorongo_In_Game'] = $cantidadxCarta[6];
      $cardsUser->save();


      for ($j = 0; $j < count($stockxCarta); $j++) {
        Cards_game::where('idMesa', $request->idMesa)
          ->where('idCard', $j + 1)
          ->update([
            'cardStock' => $stockxCarta[$j]
          ]);
      }
      $cantidadCartasXUsuario = 6;
      $cantidadxCarta = [];
      $stockxCarta = [];
      $nuevoStock = [];
    }
    $cardsName = new stdClass();
    $cardsNames = [];
    $cards = Cards_user::select('card_1_In_Game', 'card_2_In_Game', 'card_3_In_Game', 'card_4_In_Game', 'card_5_In_Game', 'card_6_In_Game', 'card_Otorongo_In_Game')
      ->where('idMesa', $request->idMesa)
      ->where('idUser', $request->Participantes[0])
      ->first();

    if ($cards->card_1_In_Game > 0) {
      for ($i = 0; $i < $cards->card_1_In_Game; $i++) {
        $cardsName->nameCard = "card1";

        $cardsNamese = array(
          "idCard" => 1,
          "nameCard" => $cardsName->nameCard,
          "valor" => 1,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_2_In_Game > 0) {
      for ($i = 0; $i < $cards->card_2_In_Game; $i++) {

        $cardsName->nameCard = "card2";

        $cardsNamese = array(
          "idCard" => 2,
          "nameCard" => $cardsName->nameCard,
          "valor" => 2,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_3_In_Game > 0) {
      for ($i = 0; $i < $cards->card_3_In_Game; $i++) {
        $cardsName->nameCard = "card3";

        $cardsNamese = array(
          "idCard" => 3,
          "nameCard" => $cardsName->nameCard,
          "valor" => 3,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_4_In_Game > 0) {
      for ($i = 0; $i < $cards->card_4_In_Game; $i++) {
        $cardsName->nameCard = "card4";

        $cardsNamese = array(
          "idCard" => 4,
          "nameCard" => $cardsName->nameCard,
          "valor" => 4,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_5_In_Game > 0) {
      for ($i = 0; $i < $cards->card_5_In_Game; $i++) {
        $cardsName->nameCard = "card5";

        $cardsNamese = array(
          "idCard" => 5,
          "nameCard" => $cardsName->nameCard,
          "valor" => 5,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_6_In_Game > 0) {
      for ($i = 0; $i < $cards->card_6_In_Game; $i++) {
        $cardsName->nameCard = "card6";

        $cardsNamese = array(
          "idCard" => 6,
          "nameCard" => $cardsName->nameCard,
          "valor" => 6,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_Otorongo_In_Game > 0) {
      for ($i = 0; $i < $cards->card_Otorongo_In_Game; $i++) {
        $cardsName->nameCard = "cardOtorongo";

        $cardsNamese = array(
          "idCard" => 7,
          "nameCard" => $cardsName->nameCard,
          "valor" => 10,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    $cardsMesa = Cards_game::select('idCard', 'cardStock')
      ->where('idMesa', $request->idMesa)
      ->get();
    for ($i = 0; $i < count($cardsMesa); $i++) {
      if ($cardsMesa[$i]->cardStock == 0) {
        unset($cardsMesa[$i]);
      }
    }

    //nos aseguramos que estamos enviando una carta con stock mayor a 0
    $cardMesa = $cardsMesa->random();


    if ($cardMesa->cardStock > 0) {
      $stockNew = $cardMesa->cardStock - 1;

      $cardMesaName = new stdClass();
      switch ($cardMesa->idCard) {
        case 1:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_1_In_Mesa'] = 1;
          $cardMesaNew->save();

          $cardMesaName->nameCard = "card1";
          $cardMesaName->valor = 1;
          break;
        case 2:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_2_In_Mesa'] = 1;
          $cardMesaNew->save();

          $cardMesaName->nameCard = "card2";
          $cardMesaName->valor = 2;
          break;
        case 3:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_3_In_Mesa'] = 1;
          $cardMesaNew->save();

          $cardMesaName->nameCard = "card3";
          $cardMesaName->valor = 3;
          break;
        case 4:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_4_In_Mesa'] = 1;
          $cardMesaNew->save();

          $cardMesaName->nameCard = "card4";
          $cardMesaName->valor = 4;
          break;
        case 5:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_5_In_Mesa'] = 1;
          $cardMesaNew->save();


          $cardMesaName->nameCard = "card5";
          $cardMesaName->valor = 5;
          break;
        case 6:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_6_In_Mesa'] = 1;
          $cardMesaNew->save();


          $cardMesaName->nameCard = "card6";
          $cardMesaName->valor = 6;
          break;
        case 7:
          $cardMesaNew = new Cards_mesa();
          $cardMesaNew['idMesa'] = $request->idMesa;
          $cardMesaNew['card_Otorongo_In_Mesa'] = 1;
          $cardMesaNew->save();


          $cardMesaName->nameCard = "cardOtorongo";
          $cardMesaName->valor = 10;
          break;
      }


      Cards_game::where('idMesa', $request->idMesa)
        ->where('idCard', $cardMesa->idCard)
        ->update(['cardStock' => $stockNew]);

      return response()->json([
        'message' => "se registraron las cartas de todos los participantes",
        'cards' => $cardsNames,
        'cardMesaName' => $cardMesaName
      ]);
    } else {
      return response()->json([
        'message' => "ocurrio un problema al asignar una carta a la mesa"
      ]);
    }
  }

  public function getCardsUser(Request $request)
  {

    $cardsName = new stdClass();
    $cardsNames = [];

    $cards = Cards_user::where('idMesa', $request->idMesa)
      ->where('idUser', $request->idUser)
      ->first();

    if ($cards->card_1_In_Game > 0) {
      for ($i = 0; $i < $cards->card_1_In_Game; $i++) {
        $cardsName->nameCard = "card1";

        $cardsNamese = array(
          "idCard" => 1,
          "nameCard" => $cardsName->nameCard,
          "valor" => 1,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_2_In_Game > 0) {
      for ($i = 0; $i < $cards->card_2_In_Game; $i++) {

        $cardsName->nameCard = "card2";

        $cardsNamese = array(
          "idCard" => 2,
          "nameCard" => $cardsName->nameCard,
          "valor" => 2,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_3_In_Game > 0) {
      for ($i = 0; $i < $cards->card_3_In_Game; $i++) {
        $cardsName->nameCard = "card3";

        $cardsNamese = array(
          "idCard" => 3,
          "nameCard" => $cardsName->nameCard,
          "valor" => 3,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_4_In_Game > 0) {
      for ($i = 0; $i < $cards->card_4_In_Game; $i++) {
        $cardsName->nameCard = "card4";

        $cardsNamese = array(
          "idCard" => 4,
          "nameCard" => $cardsName->nameCard,
          "valor" => 4,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_5_In_Game > 0) {
      for ($i = 0; $i < $cards->card_5_In_Game; $i++) {
        $cardsName->nameCard = "card5";

        $cardsNamese = array(
          "idCard" => 5,
          "nameCard" => $cardsName->nameCard,
          "valor" => 5,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_6_In_Game > 0) {
      for ($i = 0; $i < $cards->card_6_In_Game; $i++) {
        $cardsName->nameCard = "card6";

        $cardsNamese = array(
          "idCard" => 6,
          "nameCard" => $cardsName->nameCard,
          "valor" => 6,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_Otorongo_In_Game > 0) {
      for ($i = 0; $i < $cards->card_Otorongo_In_Game; $i++) {
        $cardsName->nameCard = "cardOtorongo";

        $cardsNamese = array(
          "idCard" => 7,
          "nameCard" => $cardsName->nameCard,
          "valor" => 10,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }


    return response()->json([
      'message' => "cartas en mano del Participante",
      'cards' => $cardsNames
    ]);
  }

  public function getcardMesa(Request $request)
  {
    $cardMesa = Cards_mesa::where('idMesa', $request->idMesa)->first();

    $cardMesaName = new stdClass();
    if ($cardMesa->card_1_In_Mesa == 1) {

      $cardMesaName->nameCard = "card1";
      $cardMesaName->valor = 1;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_2_In_Mesa == 1) {

      $cardMesaName->nameCard = "card2";
      $cardMesaName->valor = 2;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_3_In_Mesa == 1) {

      $cardMesaName->nameCard = "card3";
      $cardMesaName->valor = 3;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_4_In_Mesa == 1) {

      $cardMesaName->nameCard = "card4";
      $cardMesaName->valor = 4;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_5_In_Mesa == 1) {

      $cardMesaName->nameCard = "card5";
      $cardMesaName->valor = 5;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_6_In_Mesa == 1) {

      $cardMesaName->nameCard = "card6";
      $cardMesaName->valor = 6;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
    if ($cardMesa->card_Otorongo_In_Mesa == 1) {

      $cardMesaName->nameCard = "cardOtorongo";
      $cardMesaName->valor = 10;

      return response()->json([
        'cards' => $cardMesaName
      ]);
    }
  }

  //cuando pones una carta en la mesa
  public function cardUserUpdate(Request $request)
  {

    switch ($request->idCard) {

      case 1:

        $stock = Cards_user::select('card_1_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_1_In_Game' => $stock->card_1_In_Game - 1
          ]);
        break;

      case 2:

        $stock = Cards_user::select('card_2_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_2_In_Game' => $stock->card_2_In_Game - 1
          ]);
        break;
      case 3:

        $stock = Cards_user::select('card_3_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_3_In_Game' => $stock->card_3_In_Game - 1
          ]);
        break;
      case 4:

        $stock = Cards_user::select('card_4_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_4_In_Game' => $stock->card_4_In_Game - 1
          ]);
        break;
      case 5:

        $stock = Cards_user::select('card_5_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_5_In_Game' => $stock->card_5_In_Game - 1
          ]);
        break;
      case 6:

        $stock = Cards_user::select('card_6_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_6_In_Game' => $stock->card_6_In_Game - 1
          ]);
        break;
      case 7:

        $stock = Cards_user::select('card_Otorongo_In_Game')
          ->where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->first();

        Cards_user::where('idMesa', $request->idMesa)
          ->where('idUser', $request->idUser)
          ->update([
            'card_Otorongo_In_Game' => $stock->card_Otorongo_In_Game - 1
          ]);
        break;
    }

    return response()->json([
      'mensaje' => "se resto uno del stock de la carta",
      "user" => $request->idUser
    ]);
  }


  public function robarCarta(Request $request)
  {

    $sumaStock = Cards_game::where('idMesa', $request->idMesa)->get()->sum('cardStock');
    if ($sumaStock > 0) {

      $cardsName = new stdClass();
      $card = [];

      //elegir una carta random
      $random = Cards_game::where('idMesa', $request->idMesa)
        ->where('cardStock', '>', 0)
        ->get()->random();

      switch ($random->idCard) {

        case 1:
          $cardsName->nameCard = "card1";
          $cardsNamese = array(
            "idCard" => 1,
            "nameCard" => $cardsName->nameCard,
            "valor" => 1,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          //esa misma carta sumarle al cards_user
          $stock = Cards_user::select('card_1_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_1_In_Game' => $stock->card_1_In_Game + 1
            ]);
          break;

        case 2:
          $cardsName->nameCard = "card2";
          $cardsNamese = array(
            "idCard" => 2,
            "nameCard" => $cardsName->nameCard,
            "valor" => 2,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_2_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_2_In_Game' => $stock->card_2_In_Game + 1
            ]);
          break;
        case 3:
          $cardsName->nameCard = "card3";
          $cardsNamese = array(
            "idCard" => 3,
            "nameCard" => $cardsName->nameCard,
            "valor" => 3,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_3_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_3_In_Game' => $stock->card_3_In_Game + 1
            ]);
          break;

        case 4:
          $cardsName->nameCard = "card4";
          $cardsNamese = array(
            "idCard" => 4,
            "nameCard" => $cardsName->nameCard,
            "valor" => 4,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);
          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_4_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_4_In_Game' => $stock->card_4_In_Game + 1
            ]);
          break;

        case 5:
          $cardsName->nameCard = "card5";
          $cardsNamese = array(
            "idCard" => 5,
            "nameCard" => $cardsName->nameCard,
            "valor" => 5,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_5_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_5_In_Game' => $stock->card_5_In_Game + 1
            ]);
          break;

        case 6:
          $cardsName->nameCard = "card6";
          $cardsNamese = array(
            "idCard" => 6,
            "nameCard" => $cardsName->nameCard,
            "valor" => 6,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_6_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_6_In_Game' => $stock->card_6_In_Game + 1
            ]);
          break;

        case 7:
          $cardsName->nameCard = "cardOtorongo";
          $cardsNamese = array(
            "idCard" => 7,
            "nameCard" => $cardsName->nameCard,
            "valor" => 10,
            "esMayorIgual" => 0
          );
          array_push($card, $cardsNamese);

          //la carta random elegida descontarle al card_game
          Cards_game::where('idMesa', $request->idMesa)
            ->where('idCard', $random->idCard)
            ->update([
              'cardStock' => $random->cardStock - 1
            ]);

          $stock = Cards_user::select('card_Otorongo_In_Game')
            ->where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->first();

          Cards_user::where('idMesa', $request->idMesa)
            ->where('idUser', $request->idUser)
            ->update([
              'card_Otorongo_In_Game' => $stock->card_Otorongo_In_Game + 1
            ]);
          break;
      }

      return response()->json([
        'mensaje' => "se le sumo uno carta al usuario y se actualizo el stock del maso",
        "user" => $request->idUser,
        "idCard" => $random->idCard,
        "card" => $card,
        "stock" => $sumaStock
      ]);
    } else {
      return response()->json([
        'mensaje' => "no hay mas cartas para robar del mazo",
        'stock' => $sumaStock
      ]);
    }
  }
  public function updateCardsGame(Request $request)
  {

    $nroDeUsuarios = count($request->Participantes);
    $nroDeBarajas = 0;
    $cartasXtipoXBaraja = 8; //numero fijo para hacer un total de 56 cartas al multiplicar por tiposCartas
    $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
    $tiposCartas = 7; //(1,2,3,4,5,6,Otorongo)
    $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
    $cartasAjugar = $Mazo / 2;
    $valor = 0;
    $Sumvalor = 0;
    $cartasAjugarmenosStock = 0;
    //  $nroRandom = 0;

    //definimos el numero de barajas segun el numero de participantes
    if ($nroDeUsuarios < 4) {

      $nroDeBarajas = 1;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    } elseif ($nroDeUsuarios < 7) {
      return response()->json([
        'message' => "entro al if - de 7",
        'nroDeUsuarios' => $nroDeUsuarios
      ]);
      $nroDeBarajas = 2;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    } elseif ($nroDeUsuarios < 10) {
      return response()->json([
        'message' => "entro al if - de 10",
        'nroDeUsuarios' => $nroDeUsuarios
      ]);
      $nroDeBarajas = 3;
      $nroCartasXtipoXmazo = $nroDeBarajas * $cartasXtipoXBaraja;
      $Mazo = $nroCartasXtipoXmazo * $tiposCartas;
      $cartasAjugar = $Mazo / 2;
      $cartasAjugarmenosStock = $Mazo / 2;
    }


    //llenamos cantidad de cartas por tipo de carta(1,2,3,4,5,6,Otorongo)
    for ($i = 1; $i < 8; $i++) {

      if ($i == 7) {
        $valor = $cartasAjugar - $Sumvalor;

        $nroRandom = mt_rand(0, $cartasAjugarmenosStock);
        $dividirCosiente = intval($nroRandom / $nroCartasXtipoXmazo);

        Cards_game::where("idMesa", $request->idMesa)
          ->where("idCard", $i)
          ->update([
            'cardStock' => $valor
          ]);

        $cartasAjugarmenosStock = $cartasAjugarmenosStock - $valor; //nuevo valor cartasAjugar para llenar la proxima carta
        $Sumvalor = $Sumvalor + $valor;

        $cards = Cards_game::where('idMesa', $request->idMesa)->get();
        return response()->json([
          'message' => "se registraron las cartas a jugar",
          'cards' => $cards,
          '$cartas A jugar' => $cartasAjugar,
          'otorongo' => $valor,
          '$Sumvalor' => $Sumvalor
        ]);
      }

      $nroRandom = mt_rand(0, $cartasAjugarmenosStock);
      $dividirCosiente = intval($nroRandom / $nroCartasXtipoXmazo);
      $valor  = $nroCartasXtipoXmazo * $dividirCosiente;
      $valor = $nroRandom - $valor;

      Cards_game::where("idMesa", $request->idMesa)
        ->where("idCard", $i)
        ->update([
          'cardStock' => $valor
        ]);
      $cartasAjugarmenosStock = $cartasAjugarmenosStock - $valor; //nuevo valor cartasAjugar para llenar la proxima carta
      $Sumvalor = $Sumvalor + $valor;
    }
  }

  //para cuando se vuelve a repartir las cartas
  public function updateCardsUser(Request $request)
  {

    $cantidadCartasXUsuario = 6;
    $cantidadxCarta = []; //las cartas que se le descontara al stock
    $stockxCarta = [];
    $nuevoStock = [];
    $nroDeUsuarios = count($request->Participantes);

    //resetear los registros de cartas en mesa
    Cards_mesa::where("idMesa",$request->idMesa)
    ->update([
      'card_1_In_Mesa' => 0,
      'card_2_In_Mesa' => 0,
      'card_3_In_Mesa' => 0,
      'card_4_In_Mesa' => 0,
      'card_5_In_Mesa' => 0,
      'card_6_In_Mesa' => 0,
      'card_Otorongo_In_Mesa' => 0
    ]);
    for ($ili = 0; $ili < $nroDeUsuarios; $ili++) {

      //1.-se consulta la tabla cards_game segun el idMesa y se obtiene un arreglo de 7 registros
      $cards = Cards_game::where('idMesa', $request->idMesa)->get();

      $cantidadCartas = count($cards);

      //2.-se crean los registros en la tabla cards_users apartir de la variable $cards


      for ($i = 0; $i < $cantidadCartas; $i++) {

        //llenamos un array para con el stock actual de las cartas en cards_game
        array_push($stockxCarta, $cards[$i]->cardStock);

        $nroRandom = mt_rand(0, $cantidadCartasXUsuario);

        //comprobamos que la carta tenga stock para repartir al jugador
        if ($cards[$i]->cardStock == 0) {


          $valor = 0;

          //le digo que al otorongo siempre le de las cartas que sobraron
          if ($i == 6) {
            $valor = $cantidadCartasXUsuario;
          }
        } else {
          $dividirCosiente = intval($nroRandom / $cards[$i]->cardStock);
          $valor =  $cards[$i]->cardStock * $dividirCosiente;
          $valor = $nroRandom - $valor;

          //le digo que al otorongo siempre le de las cartas que sobraron
          if ($i == 6) {
            $valor = $cantidadCartasXUsuario;
          }
        }

        //agregamos a un array la cantidad de cartas para descontarlo del stock al final
        array_push($cantidadxCarta, $valor);
        $cantidadCartasXUsuario = $cantidadCartasXUsuario - $valor;
      }

      for ($k = 0; $k < count($cantidadxCarta); $k++) {
        $nuevovalor = abs($stockxCarta[$k] - $cantidadxCarta[$k]); // con abs eliminamos los valores negativos que se obtienen al restar 0 - 2 por ejemplo
        array_push($nuevoStock, $nuevovalor);
      }

      //le doy los nuevos valores al stock de cada carta para el siguiente jugador
      $stockxCarta = $nuevoStock;

      //se actualizan el stock de cartas de cada participante
      Cards_user::where("idMesa", $request->idMesa)
        ->where("idUser", $request->Participantes[$ili])
        ->update([
          'card_1_In_Game' => $cantidadxCarta[0],
          'card_2_In_Game' => $cantidadxCarta[1],
          'card_3_In_Game' => $cantidadxCarta[2],
          'card_4_In_Game' => $cantidadxCarta[3],
          'card_5_In_Game' => $cantidadxCarta[4],
          'card_6_In_Game' => $cantidadxCarta[5],
          'card_Otorongo_In_Game' => $cantidadxCarta[6]
        ]);


      for ($j = 0; $j < count($stockxCarta); $j++) {
        Cards_game::where('idMesa', $request->idMesa)
          ->where('idCard', $j + 1)
          ->update([
            'cardStock' => $stockxCarta[$j]
          ]);
      }
      $cantidadCartasXUsuario = 6;
      $cantidadxCarta = [];
      $stockxCarta = [];
      $nuevoStock = [];
    }

    $cardsName = new stdClass();
    $cardsNames = [];
    $cards = Cards_user::select('card_1_In_Game', 'card_2_In_Game', 'card_3_In_Game', 'card_4_In_Game', 'card_5_In_Game', 'card_6_In_Game', 'card_Otorongo_In_Game')
      ->where('idMesa', $request->idMesa)
      ->where('idUser', $request->Participantes[0])
      ->first();

    if ($cards->card_1_In_Game > 0) {
      for ($i = 0; $i < $cards->card_1_In_Game; $i++) {
        $cardsName->nameCard = "card1";

        $cardsNamese = array(
          "idCard" => 1,
          "nameCard" => $cardsName->nameCard,
          "valor" => 1,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_2_In_Game > 0) {
      for ($i = 0; $i < $cards->card_2_In_Game; $i++) {

        $cardsName->nameCard = "card2";

        $cardsNamese = array(
          "idCard" => 2,
          "nameCard" => $cardsName->nameCard,
          "valor" => 2,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_3_In_Game > 0) {
      for ($i = 0; $i < $cards->card_3_In_Game; $i++) {
        $cardsName->nameCard = "card3";

        $cardsNamese = array(
          "idCard" => 3,
          "nameCard" => $cardsName->nameCard,
          "valor" => 3,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_4_In_Game > 0) {
      for ($i = 0; $i < $cards->card_4_In_Game; $i++) {
        $cardsName->nameCard = "card4";

        $cardsNamese = array(
          "idCard" => 4,
          "nameCard" => $cardsName->nameCard,
          "valor" => 4,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_5_In_Game > 0) {
      for ($i = 0; $i < $cards->card_5_In_Game; $i++) {
        $cardsName->nameCard = "card5";

        $cardsNamese = array(
          "idCard" => 5,
          "nameCard" => $cardsName->nameCard,
          "valor" => 5,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_6_In_Game > 0) {
      for ($i = 0; $i < $cards->card_6_In_Game; $i++) {
        $cardsName->nameCard = "card6";

        $cardsNamese = array(
          "idCard" => 6,
          "nameCard" => $cardsName->nameCard,
          "valor" => 6,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    if ($cards->card_Otorongo_In_Game > 0) {
      for ($i = 0; $i < $cards->card_Otorongo_In_Game; $i++) {
        $cardsName->nameCard = "cardOtorongo";

        $cardsNamese = array(
          "idCard" => 7,
          "nameCard" => $cardsName->nameCard,
          "valor" => 10,
          "esMayorIgual" => 0
        );
        array_push($cardsNames, $cardsNamese);
      }
    }
    $cardsMesa = Cards_game::select('idCard', 'cardStock')
      ->where('idMesa', $request->idMesa)
      ->get();
    for ($i = 0; $i < count($cardsMesa); $i++) {
      if ($cardsMesa[$i]->cardStock == 0) {
        unset($cardsMesa[$i]);
      }
    }

    //nos aseguramos que estamos enviando una carta con stock mayor a 0
    $cardMesa = $cardsMesa->random();


    if ($cardMesa->cardStock > 0) {
      $stockNew = $cardMesa->cardStock - 1;

      $cardMesaName = new stdClass();
      switch ($cardMesa->idCard) {
        case 1:

         Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_1_In_Mesa' => 1]);
          
          $cardMesaName->nameCard = "card1";
          $cardMesaName->valor = 1;
          break;
        case 2:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_2_In_Mesa' => 1]);

          $cardMesaName->nameCard = "card2";
          $cardMesaName->valor = 2;
          break;
        case 3:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_3_In_Mesa' => 1]);

          $cardMesaName->nameCard = "card3";
          $cardMesaName->valor = 3;
          break;
        case 4:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_4_In_Mesa' => 1]);

          $cardMesaName->nameCard = "card4";
          $cardMesaName->valor = 4;
          break;
        case 5:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_5_In_Mesa' => 1]);

          $cardMesaName->nameCard = "card5";
          $cardMesaName->valor = 5;
          break;
        case 6:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_6_In_Mesa' => 1]);

          $cardMesaName->nameCard = "card6"; 
          $cardMesaName->valor = 6;
          break; 
        case 7:
          Cards_mesa::where('idMesa', $request->idMesa)
         ->update(['card_Otorongo_In_Mesa' => 1]);

          $cardMesaName->nameCard = "cardOtorongo";
          $cardMesaName->valor = 10;
          break;
      }


      Cards_game::where('idMesa', $request->idMesa)
        ->where('idCard', $cardMesa->idCard)
        ->update(['cardStock' => $stockNew]);

      return response()->json([
        'message' => "se registraron las cartas de todos los participantes",
        'cards' => $cardsNames,
        'cardMesaName' => $cardMesaName
      ]);
    } else {
      return response()->json([
        'message' => "ocurrio un problema al asignar una carta a la mesa"
      ]);
    }
  }

  public function getcartasMazo(Request $request){
    $cartasMazo =  Cards_game::select('cardStock')
     ->where('idMesa', $request->idMesa)
     ->get()
     ->sum('cardStock');


     return response()->json([
      'cartasMazo' => $cartasMazo
    ]);
  }
}
