<?php

namespace App\Http\Controllers;

use App\Models\publicaciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicacionesController extends Controller
{
    protected $publicaciones;
    public function __construct(){
        $this->publicaciones = new publicaciones;
    }
    public function add(Request $request)
    {
       $this->publicaciones->user_id= $request->user_id;
       $user = User::find($this->publicaciones->user_id);
        $file_name = "";
        $this->publicaciones->description= $request->description;
        $this->publicaciones->image = $file_name;
       $imagenes = $request->file("image")->store("public/imagenes");
       $image = Storage::url($imagenes);
       $publicacion = publicaciones::Create([
        'description'=>$this->publicaciones->description,
        'image' => $image,
        'user_id'=>$this->publicaciones->user_id
         ]);
        // $user->publicaciones()->create([
        //     'description'=>$this->publicaciones->description,
        //     'image' => $image,
        //  ]);
            return response()->json([
                "success"=>true,
                "message"=>"Registro con éxito",
                "user"=>$user
            ],200);
    }
    public function getAll()
    {
        $bp = User::with('publicaciones')->get();
        // $user = View::make('view_name')->with('baseProduct', $bp);
        //  $user = User::find($this->publicaciones->user_id);
    	$publicacio = publicaciones::all();
        return response()->json([
            "success"=>true,
            "message"=>"Registro con éxito",
            "user"=>$bp,
                
        ],200);
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
