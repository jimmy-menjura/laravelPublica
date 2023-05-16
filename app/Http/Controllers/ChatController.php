<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatPrivate;
use App\Events\ChatEvent;
use App\Models\mensajes;
use Illuminate\Support\Facades\DB;

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

        mensajes::create([
            'user_auth' => auth()->user()->id,
            'message' => $request->get('message') ,
            'to' => $request->get('to')
        ]);

        return response()->json([
            'ok'    => true,
            'message'   => 'Mensaje enviado correctamente',
        ]);
    }
    public function getMessagePriv($id){
        $messageByUser = DB::table('mensajes')
            ->join('users','users.id','=','mensajes.user_auth')
            // -orWhere('users.id','=','mensajes.to')
            ->where('user_auth', '=', auth()->user()->id)
            ->where('to', '=', $id)
            ->orwhere('user_auth','=' , $id)
            ->where('to','=', auth()->user()->id)
            // ->orderBy('mensajes.created_at', 'desc')
            ->get(['mensajes.message','users.nickname',
            'users.fullname','mensajes.user_auth',
            'mensajes.to','users.id','users.image',
            'mensajes.created_at']);
        return $messageByUser;
    }

}
