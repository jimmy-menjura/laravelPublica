<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\Notifications;
use Illuminate\Support\Facades\DB;
use App\Models\Notificaciones;


class NotificationsController extends Controller
{
    public static $userId = null;
    public function __construct(){
       
    }
    public function getNotificationByUserAuth(){
        $notificaciones =  DB::table('notificaciones')
            ->where('notificaciones.to','=',self::$userId == null ?  auth()->user()->id : self::$userId)
            ->get();
        return $notificaciones;
    }
    public function deleteNotification($id){
        $notify = Notificaciones::find($id);
        $notify->delete();
        return $notify;
    }
    public function updateNotificationStatus(Request $request){
        $notifcacion = Notificaciones::find($request->id);
        $notifcacion->status = $request->status;
        $actualizado = $notifcacion->save();

        // $publicacion = $this->get($id);
        // $publicacion->fill($request->all())->save();
        if ( $actualizado )
        {
            return response()->json([
            "resp" => true,
            "Mensaje" => 'Actualizado exitosamente'
            ],200);
        }
        else{
            return 'no actualizado';
        }
    }
}
