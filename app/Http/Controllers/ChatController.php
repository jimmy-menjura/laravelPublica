<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatPrivate;

class ChatController extends Controller
{
   
    public function message(Request $request){
        broadcast(new ChatPrivate($request->message))->toOthers();
        return response()->json([
            "true" => 200,
            "Mensaje" => 'Mensaje enviado'
        ]);
    }
}
