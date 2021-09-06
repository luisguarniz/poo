<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cards_mesa;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesasController extends Controller
{
  public function makeMesa(Request $request)
  {
    //////////////////////////creo la tabla mesa////////////////////////////////////////////////////
    $game = new Mesa;
    $game['idSessionGame'] = $request->idSessionGame;
    $game->save();

    return response()->json([
      'message' => "se creo una mesa",
      'idMesa' => $game->id
    ]);
  }


  // se tiene que desactivar al iniciar otro juego
  public function desactivarMesa(Request $request)
  {

     Mesa::where('id', $request->idMesa)->update([
      'IsActive' => '0'
    ]);
  }

  public function cardMesaUpdate(Request $request)
  {


    switch ($request->idCard) {
      case 1:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 1, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 2:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 1, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 3:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 1, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 4:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 1, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 5:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 1, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 6:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 1, 'card_Otorongo_In_Mesa' => 0
          ]);
          break;
      case 7:
        $cardsMesa = Cards_mesa::where('idMesa', $request->idMesa)
          ->update([
            'card_1_In_Mesa' => 0, 'card_2_In_Mesa' => 0, 'card_3_In_Mesa' => 0, 'card_4_In_Mesa' => 0, 'card_5_In_Mesa' => 0, 'card_6_In_Mesa' => 0, 'card_Otorongo_In_Mesa' => 1
          ]);
          break;
    }

    return response()->json([
      "message" => "se actualizo la carta de la mesa",
    ]);
  }
}
