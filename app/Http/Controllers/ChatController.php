<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatPrivate;
use App\Events\ChatEvent;

class ChatController extends Controller
{
   
    public function message(Request $request){
        broadcast(new ChatPrivate($request->message))->toOthers();
        return response()->json([
            "true" => 200,
            "Mensaje" => 'Mensaje enviado'
        ]);
    }
    public function messagePriv(Request $request) {
        $data = $request->only(['message','image', 'to']);
        event(new ChatEvent($data));

        return response()->json([
            'ok'    => true,
            'message'   => 'Mensaje enviado correctamente',
        ]);
    }
}
