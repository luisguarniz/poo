<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
      
          $room = Mesa::where('id', $request->idMesa)->update([
            'IsActive' => '0'
          ]);
        }
}
