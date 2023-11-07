<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controlador;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Amigos;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('login',[Controlador::class,'getAll']);
// Route::post('create',[Controlador::class,'add']);
// Route::get('registro/{id}',[Controlador::class,'get']);


/// ..............::::::::.. Publicaciones ............:.::::::.......


Route::group([
    'middleware' => ['cors']
  //   'namespace' => ['App\Http\Controllers\Controlador']
], function ($router) {

Route::delete('eliminar/{id}',[PublicacionesController::class,'eliminar']);
Route::get('obtenerPerfil/{id}',[Controlador::class,'get']);
Route::post('mensaje', [ChatController::class,'message'])->name('api.mensaje.message')->middleware('auth:api');
Route::post('mensajePrivado', [ChatController::class,'messagePriv'])->name('api.mensaje.messagePriv')->middleware('auth:api');
// Route::post('notiSolicitud', [NotificationsController::class,'notificationbyUserFriend'])->name('api.mensaje.notification')->middleware('auth:api');
Route::post('login', [AuthController::class,'login']);
Route::post('registro', [Controlador::class,'register']);
// Route::post('editarEstadoAmigo/{id}', [Controlador::class,'editFriend']);
// Route::get('obtenerStatusFriend',[Controlador::class,'getAllFriends']);
//Route::get('usuarios', [Controlador::class,'obtenerUsuario']);

//chat 
Route::get('chat/{chat_id}', [ChatController::class,'show']);
});
//// LOGIN AUTENTICATION /////////
Route::group([
      'middleware' => ['jwt.verify','cors'],
    //   'namespace' => ['App\Http\Controllers\Controlador']
], function ($router) {
    Route::get('publicacion',[PublicacionesController::class,'getAll']);
    Route::post('crearPublicacion',[PublicacionesController::class,'add']);
    Route::get('obtenerPublicacion/{id}',[PublicacionesController::class,'get']);
    Route::post('actualizaPublicacion/{id}',[PublicacionesController::class,'editar']);
    Route::get('obtenerPublicacionPorUsuarioAuth/{id}',[PublicacionesController::class,'getPublicationByUser']);
    Route::get('private-chat/{chatroom}',[MensajeController::class,'index']);
    Route::post('private-chat/{chatroom}', [MensajeController::class,'store']);
    Route::get('fetch-private-chat/{chatroom}/',  [MensajeController::class,'get']);
    Route::get('usuarios', [Controlador::class,'index']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', [AuthController::class,'getAuthenticatedUser']);
    Route::post('agregarAmigo', [Amigos::class,'createFriend']);
    Route::put('editarEstadoAmigo/{id}', [Amigos::class,'editFriend']);
    Route::get('obtenerStatusFriend',[Amigos::class,'getAllFriends']);
    Route::delete('eliminarAmigo/{id}/{idnotify}',[Amigos::class,'eliminarFriend']);
    Route::get('amigoAgregado', [Amigos::class,'obtenerAmigosAgregados']);
    // Route::put('actualizaPerfil/{id}', [Controlador::class,'editarPerfil']);
    Route::put('actualizaPerfil/{id}',[Controlador::class,'editarPerfil']);
    Route::post('actualizarImagen/{id}',[Controlador::class,'guardarImagenPerfil']); 
    Route::post('verificatedPassword',[Controlador::class,'verificatedPassword']);
    Route::post('updatePass',[Controlador::class,'updatePass']);
    Route::delete('eliminarPublicacion/{id}',[PublicacionesController::class,'eliminar']);
    Route::get('obtenerMensajePrivado/{id}', [ChatController::class,'getMessagePriv']);
    Route::post('saveLike',[LikeController::class,'saveLike']);
    Route::delete('eliminarLike/{id}',[LikeController::class,'deleteLike']);
    Route::get('getLike/{id}',[LikeController::class,'get']);
    Route::get('getLikeByUserAndPublication/{id}',[LikeController::class,'getLikeByUserAndPublication']);
    Route::post('saveComment',[CommentController::class,'saveComment']);
    Route::delete('deleteComment/{id}',[CommentController::class,'deleteComment']);
    Route::get('getComment/{id}',[CommentController::class,'get']);
    Route::get('getCommentByUserAndPublication/{id}',[CommentController::class,'getCommentByUserAndPublication']);
    Route::put('actualizarPrivacidadPublicaciones/{id}', [Controlador::class,'updatePrivacityPublications']);
    Route::get('obtenerPrivacidadPublicación',[Controlador::class,'getPrivacityPublication']);
    Route::get('contadorMisPublicaciones',[PublicacionesController::class,'getCountMyPublications']);
    Route::get('getNotify', [NotificationsController::class,'getNotificationByUserAuth']); 
    Route::post('notiSolicitud', [NotificationsController::class,'notificationbyUserFriend']); 
    // Route::delete('deleteNotify/{id}', [NotificationsController::class,'deleteNotification']); 
    Route::put('updateStatusNotify', [NotificationsController::class,'updateNotificationStatus']); 
});
