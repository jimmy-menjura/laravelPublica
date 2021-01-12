<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controlador;
use App\Http\Controllers\PublicacionesController;
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

Route::get('login',[Controlador::class,'getAll']);
Route::post('create',[Controlador::class,'add']);
Route::get('registro/{id}',[Controlador::class,'get']);


/// ..............::::::::.. Publicaciones ............:.::::::.......


Route::get('publicacion',[PublicacionesController::class,'getAll']);
Route::post('crearPublicacion',[PublicacionesController::class,'add']);
Route::get('obtenerPublicacion/{id}',[PublicacionesController::class,'get']);
Route::post('editar/{id}',[PublicacionesController::class,'editar']);
Route::delete('eliminar/{id}',[PublicacionesController::class,'eliminar']);
