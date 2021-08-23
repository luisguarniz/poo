<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function messageWebsocket(Request $request){
        // $data = $request->only(['msgUnblock','codigoSesion','to']);
 
         event(new MessageEvent($request));
      
         return response()->json([
             'ok'  => true,
             'message' => 'mensaje enviado correctamente',
         ]);
     }
}
