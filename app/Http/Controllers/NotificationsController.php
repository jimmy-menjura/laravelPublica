<?php

namespace App\Http\Controllers;

use App\Models\nosendnotifies;
use App\Models\Notificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public static $userId;

    public function __construct()
    {
    }

    public function getNotificationByUserAuth()
    {
        $notificaciones = DB::table('notificaciones')
            ->where('notificaciones.to', '=', self::$userId == null ? auth()->user()->id : self::$userId)
            ->get();

        return $notificaciones;
    }

    public function deleteNotification($id)
    {
        $notify = Notificaciones::find($id);
        $notify->delete();

        return $notify;
    }

    public function updateNotificationStatus($id, Request $request)
    {
        $notifcacion = Notificaciones::find($id);
        if ($notifcacion->status != 2) {
            $notifcacion->status = $request->status;
            $actualizado = $notifcacion->save();

            // $publicacion = $this->get($id);
            // $publicacion->fill($request->all())->save();
            if ($actualizado) {
                return response()->json([
                'resp' => true,
                'Mensaje' => 'Actualizado exitosamente',
                ], 200);
            } else {
                return 'no actualizado';
            }
        } else {
            return response()->json([
                'resp' => true,
                'Mensaje' => 'Ya esta actualizado! ',
                ], 200);
        }
    }

    public function updateNotificationMessage($id, $message, $created_at)
    {
        $notifcacion = Notificaciones::find($id);
        $notifcacion->message = $message;
        $notifcacion->created_at = $created_at;
        $actualizado = $notifcacion->save();

        // $publicacion = $this->get($id);
        // $publicacion->fill($request->all())->save();
        return $actualizado;
    }

    public function noNotificationMessage(Request $request)
    {
        $noNotify = nosendnotifies::create([
            'user_send' => $request->get('user_send'),
            'user_id' => auth()->user()->id,
        ]);

        return $noNotify;
    }

    public function noNotifyMessage($id, $idTo)
    {
        $notificaciones = DB::table('nosendnotifies')
            ->where('user_send', '=', $id)
            ->where('user_id', '=', $idTo)
            ->get();

        return $notificaciones->isEmpty();
    }

    public function deleteNotify(Request $request)
    {
        $notificaciones = DB::table('nosendnotifies')
            ->where('user_send', '=', $request->get('user_send'))
            ->where('user_id', '=', auth()->user()->id)
            ->delete();
            
        return $notificaciones;
    }
}
