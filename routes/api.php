<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controlador;
use App\Http\Controllers\PublicacionesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
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
Route::get('publicacion',[PublicacionesController::class,'getAll']);
Route::post('crearPublicacion',[PublicacionesController::class,'add']);
Route::get('obtenerPublicacion/{id}',[PublicacionesController::class,'get']);
Route::post('editar/{id}',[PublicacionesController::class,'editar']);
Route::delete('eliminar/{id}',[PublicacionesController::class,'eliminar']);
Route::get('obtenerPerfil/{id}',[Controlador::class,'get']);
Route::post('mensaje', [ChatController::class,'message'])->name('api.mensaje.message')->middleware('auth:api');
Route::post('mensajePrivado', [ChatController::class,'messagePriv'])->name('api.mensaje.messagePriv')->middleware('auth:api');
Route::post('agregarAmigo', [Controlador::class,'createFriend']);
Route::get('obtenerStatusFriend',[Controlador::class,'getAllFriends']);
Route::post('editarEstadoAmigo/{id}', [Controlador::class,'editFriend']);
//Route::get('usuarios', [Controlador::class,'obtenerUsuario']);

//chat 
Route::get('chat/{chat_id}', [ChatController::class,'show']);
});
//// LOGIN AUTENTICATION /////////
Route::group([
      'middleware' => ['jwt.verify'],
    //   'namespace' => ['App\Http\Controllers\Controlador']
], function ($router) {
    Route::get('private-chat/{chatroom}',[MensajeController::class,'index']);
    Route::post('private-chat/{chatroom}', [MensajeController::class,'store']);
    Route::get('fetch-private-chat/{chatroom}/',  [MensajeController::class,'get']);
    Route::get('usuarios', [Controlador::class,'index']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', [AuthController::class,'getAuthenticatedUser']);
    
});
Route::post('login', [AuthController::class,'login']);
Route::post('registro', [Controlador::class,'register']);
