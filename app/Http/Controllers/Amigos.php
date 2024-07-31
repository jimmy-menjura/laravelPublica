<?php

namespace App\Http\Controllers;

use App\Models\friends;
use App\Models\Notificaciones;
use App\Models\User;
use App\Notifications\notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Amigos extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
        // $this->middleware('auth:api', ['except' => ['login']]);
        $this->guard = 'api';
    }

    public function obtenerAmigosAgregados()
    {
        try {
            $UserRecep = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.user_id')
            ->where('friends.user_friend', '=', auth()->user()->id)
            ->where('friends.status', '=', 2)
            ->get();

            //     $UserRecep = DB::select(DB::raw('
            // SELECT * FROM `users` u
            // INNER JOIN friends f on u.id = f.user_id
            // where f.user_friend = '.auth()->user()->id.' and f.status = 2'
            //     ));

            // ->join('friends','users.id','=','friends.user_friend')
            // ->where('friends.user_id','=', auth()->user()->id)
            // ->get();

            $UserSend = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.user_friend')
            ->where('friends.user_id', '=', auth()->user()->id)
            ->where('friends.status', '=', 2)
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Registro con éxito',
                'UserRecep' => $UserRecep,
                'UserSend' => $UserSend,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Se ha vencido la sesión por error = '.$e,
            ], 403);
        }
    }

    public function eliminarFriend($id, $idnotify)
    {
        $notification = new NotificationsController();
        $friend = friends::find($id);
        $notification->deleteNotification($idnotify);
        $friend->delete();

        return $friend;
    }

    /*
         * Metodos para agregar y obtener  los amigos que se desean agregar
         */
    public function createFriend(Request $request)
    {
        $notification = new NotificationsController();
        $message = 'Te envió la solicitud';
        $status = 1;
        $typeNotify = 2;
        $registro = null;
        $user = User::find($request->user_id);
        $userSend = User::find($request->user_friend);
        $notification::$userId = $userSend->id;
        $getNotificationBD = $notification->getNotificationByUserAuth();
        if (count($getNotificationBD) > 0) {
            // $NoExistMessage = false;
            // foreach ($getNotificationBD as $valor) {
            //     if ($valor->user_id != $user->id || $valor->typeNotify != $typeNotify) {
            //         $NoExistMessage = true;
            //     }
            // }
            // dd($getNotificationBD);
            if (!$getNotificationBD->contains('user_id', $user->id)
            || !$getNotificationBD->contains('to', $userSend->id)
            || !$getNotificationBD->contains('typeNotify', $typeNotify)) {
                $currentTime = Carbon::now();
                $created_at = $currentTime->toDateTimeString();
                $userSend->notify(new notifications($message, $user->nickname, $user->fullname, $user->image, $status, $typeNotify, $user->id, $created_at));
                Notificaciones::create([
                    'message' => $message,
                    'nickname' => $user->nickname,
                    'fullname' => $user->fullname,
                    'image' => $user->image,
                    'status' => $status,
                    'typeNotify' => $typeNotify,
                    'user_id' => $user->id,
                    'to' => $userSend->id,
                ]);
                $registro = friends::create($request->all());
            }
        } else {
            $currentTime = Carbon::now();
            $created_at = $currentTime->toDateTimeString();
            $userSend->notify(new notifications($message, $user->nickname, $user->fullname, $user->image, $status, $typeNotify, $user->id, $created_at));
            Notificaciones::create([
                'message' => $message,
                'nickname' => $user->nickname,
                'fullname' => $user->fullname,
                'image' => $user->image,
                'status' => 1,
                'typeNotify' => 2,
                'user_id' => $user->id,
                'to' => $userSend->id,
            ]);
            $registro = friends::create($request->all());
        }

        return $registro == null ? 'Ya existe el registro' : $registro;
    }

    public function editFriend(Request $request, friends $id)
    {
        $id->update($request->all());

        // $registro = $this->get($id);
        // $registro->fill($request->all())->save();
        return response()->json([
            'resp' => true,
            'Mensaje' => 'Actualizado exitosamente',
        ], 200);
    }

    public function getAllFriends()
    {
        $allusersAndbyUser = null;
        $message = 'Te envió la solicitud';
        try {
            $UserRecep = DB::select(DB::raw("
                SELECT u.email, u.nickname, u.fullname,u.image, u.watchpublications,f.id,f.status,f.user_id,f.user_friend,n.id as idNotify FROM `users` u 
                INNER JOIN friends f on u.id = f.user_id
                INNER JOIN notificaciones n on f.user_friend = n.to
                where n.message = 'Te envió la solicitud'
                and n.user_id = u.id
                and f.user_friend = ".auth()->user()->id.' and f.status <> 2'
            ));

            // ->join('friends','users.id','=','friends.user_friend')
            // ->where('friends.user_id','=', auth()->user()->id)
            // ->get();

            $UserSend = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.user_friend')
            // ->join('', 'notificaciones.to', '=', 'friends.user_friend')
            ->leftJoin('notificaciones', function ($query) {
                $query->on('notificaciones.to', '=', 'friends.user_friend');
                $query->on('notificaciones.user_id', '=', 'friends.user_id');
            })
            // ->where('notificaciones.message','=',"'".$message."'")
            // ->where('notificaciones.user_id', '=', 'friends.user_id')
            ->where('friends.user_id', '=', auth()->user()->id)
            ->where('friends.status', '<>', 2)
            ->get(['users.email', 'users.nickname', 'users.fullname', 'users.image', 'users.watchpublications',
            'friends.id', 'friends.status', 'friends.user_id', 'friends.user_friend', 'notificaciones.id as idNotify']);

            // if(empty($UserRecep) || $UserSend ->isEmpty()){
            $allusersAndbyUser = DB::select(DB::raw('
                    SELECT * FROM users u
                    where u.id not in (
                    SELECT user_friend from friends 
                        where user_friend = '.auth()->user()->id.'
                        or user_id = '.auth()->user()->id.' 
                        )
                      and  u.id not in (
                    SELECT user_id from friends 
                        where user_friend = '.auth()->user()->id.'
                        or user_id = '.auth()->user()->id.'
                        )
                        and u.id <> '.auth()->user()->id));

            // table('users')
            // ->join('friends','users.id','<>','friends.user_friend',)
            // ->where('users.id','<>','friends.user_id')
            // ->orWhere('friends.user_id','=',null)
            // ->orWhere('friends.user_friend','=',null)
            // ->where('users.id','=', auth()->user()->id)
            // ->get();
            // }
            // if(empty($allusersAndbyUser)){
            //     $allusersAndbyUser = User::where('id', '!=', auth()->user()->id)->get();
            // print_r($allusersAndbyUser);
            // }
            return response()->json([
                'success' => true,
                'message' => 'Registro con éxito',
                'UserRecep' => $UserRecep,
                'UserSend' => $UserSend,
                'unknows' => $allusersAndbyUser,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Se ha vencido la sesión por error = '.$e,
            ], 403);
        }
            // $users = friends::all();
    }
}
