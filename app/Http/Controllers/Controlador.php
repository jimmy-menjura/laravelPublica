<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\registro;

class Controlador extends Controller
{
    public function getAll()
    {
    $registro = registro::all();
        return $registro;
    }
    public function add(Request $request)
    {
    	$registro = registro::create($request->all());
    	return $registro;
    }
    public function get($id){
        $registro = registro::find($id);
        return $registro;
    }
    // public function editar($id, Request $request){
    //     $registro = $this->get($id);
    //     $registro->fill($request->all())->save();
    //     return $registro;
    // }
    // public function eliminar($id){
    //     $registro = $this->get($id);
    //     $registro->delete();
    //     return $registro;
    // }
}
