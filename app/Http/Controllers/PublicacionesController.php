<?php

namespace App\Http\Controllers;

use App\Models\publicacio;
use Illuminate\Http\Request;

class publicacioController extends Controller
{
    public function getAll()
    {
    $publicacion = publicaciones::all();
        return $publicacion;
    }
    public function add(Request $request)
    {
    	$publicacion = publicaciones::create($request->all());
    	return $publicacion;
    }
    public function get($id){
        $publicacion = publicaciones::find($id);
        return $publicacion;
    }
    public function editar($id, Request $request){
        $publicacion = $this->get($id);
        $publicacion->fill($request->all())->save();
        return $publicacion;
    }
    public function eliminar($id){
        $publicacion = $this->get($id);
        $publicacion->delete();
        return $publicacion;
    }
}
