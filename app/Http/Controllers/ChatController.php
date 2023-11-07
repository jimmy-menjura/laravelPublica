<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatPrivate;
use App\Events\ChatEvent;
use App\Models\mensajes;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\notifications;
use Carbon\Carbon;

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
        $mensaje = 'Te envió un mensaje : ';
        $message = array();
        $user = User::find(auth()->user()->id);
        $userSend = User::find($request->to);
        $data = $request->only(['image','message', 'to']);
        $currentTime = Carbon::now();
        $created_at = $currentTime->toDateTimeString();
        array_push($message, $request->message);
        $finishMessage = end($message);
        mensajes::create([
            'user_auth' => auth()->user()->id,
            'message' => $request->get('message') ,
            'to' => $request->get('to')
        ]);
        $userSend->notify(new notifications($mensaje . $finishMessage ,$user->nickname,$user->fullname,$user->image,$created_at));
        event(new ChatEvent($data));
        Notificaciones::create([
            'message' => $mensaje,
            'nickname' => $user->nickname,
            'fullname' => $user->fullname,
            'image' => $user->image,
            'status' => 1,
            'user_id' => $user->id,
            'to' => $userSend->id
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
