<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Events\ChatPrivate;
use App\Models\mensajes;
use App\Models\Notificaciones;
use App\Models\User;
use App\Notifications\notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function message(Request $request)
    {
        broadcast(new ChatPrivate($request->message))->toOthers();

        return response()->json([
            'true' => 200,
            'Mensaje' => 'Mensaje enviado',
        ]);
    }

    public function messagePriv(Request $request)
    {
        $notification = new NotificationsController();
        $mensaje = 'Te envió un mensaje : ';
        $message = [];
        $status = 1;
        $typeNotify = 1;
        $user = User::find(auth()->user()->id);
        $userSend = User::find($request->to);
        $notification::$userId = $userSend->id;
        $getNotificationBD = $notification->getNotificationByUserAuth();
        $data = $request->only(['image', 'message', 'to']);
        $currentTime = Carbon::now();
        $created_at = $currentTime->toDateTimeString();
        array_push($message, $request->message);
        $finishMessage = end($message);
        $noti = $notification->noNotifyMessage(auth()->user()->id, $userSend->id);

        $verifyExistMessage = $getNotificationBD->where('user_id', auth()->user()->id)
        ->where('to', $userSend->id)
        ->where('typeNotify', $typeNotify);

        if (count($getNotificationBD) > 0) {
            // $verifyExistMessage = $getNotificationBD->each(function ($item, $key) {
            //     if ($item->user_id == auth()->user()->id
            //     && $item->to == $userSend->id
            //     && (int) $item->typeNotify == (int) $typeNotify) {
            //         $notification->updateNotificationMessage($item->id, $mensaje.$finishMessage, $created_at);

            //         return true;
            //     }
            // });
            // $verifyExistMessage = $getNotificationBD->contains(function ($value, int $key) {
            //     return $value->user_id == auth()->user()->id
            //             && $value->to == $userSend->id
            //             && (int) $value->typeNotify == (int) $typeNotify;
            // });
            // foreach ($getNotificationBD as $valor) {
            if ($verifyExistMessage->count() == 1) {
                $notification->updateNotificationMessage($verifyExistMessage->first()->id, $mensaje.$finishMessage, $created_at);
                mensajes::create([
                    'user_auth' => auth()->user()->id,
                    'message' => $request->get('message'),
                    'to' => $request->get('to'),
                ]);
                event(new ChatEvent($data));
                if ($noti) {
                    $userSend->notify(new notifications($mensaje.$finishMessage, $user->nickname, $user->fullname, $user->image != null && $user->image != '' ? $user->image : '', $status, $typeNotify, $user->id, $created_at));
                }
            } else {
                mensajes::create([
                    'user_auth' => auth()->user()->id,
                    'message' => $request->get('message'),
                    'to' => $request->get('to'),
                ]);
                event(new ChatEvent($data));
                if ($noti) {
                    $userSend->notify(new notifications($mensaje.$finishMessage, $user->nickname, $user->fullname, $user->image != null && $user->image != '' ? $user->image : '', $status, $typeNotify, $user->id, $created_at));
                    Notificaciones::create([
                        'message' => $mensaje.$finishMessage,
                        'nickname' => $user->nickname,
                        'fullname' => $user->fullname,
                        'image' => $user->image != null && $user->image != '' ? $user->image : '',
                        'status' => $status,
                        'typeNotify' => $typeNotify,
                        'user_id' => $user->id,
                        'to' => $userSend->id,
                    ]);
                }
            }
            //     if ((int) $valor->user_id != (int) auth()->user()->id
            //     && $valor->to == $userSend->id
            //     && (int) $valor->typeNotify == (int) $typeNotify) {
            //         $noExistMessage = true;
            //     }
            // }
            // dd('existe verifyExistMessage ? '.$verifyExistMessage);
            // if ($verifyExistMessage) {
            //     dd('entra en el if');
            //     mensajes::create([
            //         'user_auth' => auth()->user()->id,
            //         'message' => $request->get('message'),
            //         'to' => $request->get('to'),
            //     ]);
            //     event(new ChatEvent($data));
            //     $userSend->notify(new notifications($mensaje.$finishMessage, $user->nickname, $user->fullname, $user->image, $status, $typeNotify, $user->id, $created_at));
            // } else {
            //     dd('entra en el else');
            //     mensajes::create([
            //         'user_auth' => auth()->user()->id,
            //         'message' => $request->get('message'),
            //         'to' => $request->get('to'),
            //     ]);
            //     $userSend->notify(new notifications($mensaje.$finishMessage, $user->nickname, $user->fullname, $user->image, $status, $typeNotify, $user->id, $created_at));
            //     event(new ChatEvent($data));
            //     Notificaciones::create([
            //         'message' => $mensaje.$finishMessage,
            //         'nickname' => $user->nickname,
            //         'fullname' => $user->fullname,
            //         'image' => $user->image,
            //         'status' => $status,
            //         'typeNotify' => $typeNotify,
            //         'user_id' => $user->id,
            //         'to' => $userSend->id,
            //     ]);
            // }
        } else {
            mensajes::create([
                'user_auth' => auth()->user()->id,
                'message' => $request->get('message'),
                'to' => $request->get('to'),
            ]);
            event(new ChatEvent($data));
            if ($noti) {
                $userSend->notify(new notifications($mensaje.$finishMessage, $user->nickname, $user->fullname, $user->image != null && $user->image != '' ? $user->image : '', $status, $typeNotify, $user->id, $created_at));
                Notificaciones::create([
                    'message' => $mensaje.$finishMessage,
                    'nickname' => $user->nickname,
                    'fullname' => $user->fullname,
                    'image' => $user->image != null && $user->image != '' ? $user->image : '',
                    'status' => $status,
                    'typeNotify' => $typeNotify,
                    'user_id' => $user->id,
                    'to' => $userSend->id,
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Mensaje enviado correctamente',
        ]);
    }

    public function getMessagePriv($id)
    {
        $messageByUser = DB::table('mensajes')
            ->join('users', 'users.id', '=', 'mensajes.user_auth')
            // -orWhere('users.id','=','mensajes.to')
            ->where('user_auth', '=', auth()->user()->id)
            ->where('to', '=', $id)
            ->orwhere('user_auth', '=', $id)
            ->where('to', '=', auth()->user()->id)
            // ->orderBy('mensajes.created_at', 'desc')
            ->get(['mensajes.message', 'users.nickname',
            'users.fullname', 'mensajes.user_auth',
            'mensajes.to', 'users.id', 'users.image',
            'mensajes.created_at']);

        return $messageByUser;
    }
}
