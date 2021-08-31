<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Session_game;
use Illuminate\Http\Request;

class Session_GameController extends Controller
{
    
    public function makeSessionGame(Request $request)
    {
        $game = new Session_game;
        $game['roomID'] = $request->roomID;
        $game->save();
      
      return response()->json([
        'message' => "se creo una session de juego",
        "idSession" => $game->id
      ]);
    }
  

  
  
  }
