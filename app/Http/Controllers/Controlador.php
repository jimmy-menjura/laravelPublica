<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\friends;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Controlador extends Controller
{
    protected $user;
    public function __construct(){
    $this->user = new User;
    // $this->middleware('auth:api', ['except' => ['login']]);
    $this->guard = "api";
     }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'string|email|max:255|unique:users',
            'password' => 'string|min:6|max:30',
            'nickName' => 'string|max:255',
            'fullName' => 'string|max:255',
            'birthdate' => 'date|max:255|',
            'image' => 'File',
            ]);

            if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }
            $file_name = "";
            $this->user->image = $file_name;
            $imagenes = $request->file("image")->store("public/archivo_imagenes");
            $image = Storage::url($imagenes);
            // $user = registro::create($request->all());
            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'nickName' => $request->get('nickName'),
                'fullName' => $request->get('fullName'),
                'birthdate' => preg_replace("/T.*/","", $request->get('birthdate')),
                'image' => $image,
                ]);

                $token = JWTAuth::fromUser($user);

                return response()->json(compact('user','token'),201);
            }
            // public function obtenerUsuario()
            // {
            //     $user = User::all();
            //     return $user;
            // }
            public function index()
            {
                $users = User::where('id', '!=', auth()->user()->id)->get();
                return $users;
            }
            public function get($id){
                $users = User::find($id);
                return $users;
            }
            /*
            * Metodos para agregar y obtener  los amigos que se desean agregar
            */
              public function createFriend(Request $request)
            {
            	$registro = friends::create($request->all());
            	return $registro;
            }

            public function editFriend($id, Request $request){
                $registro = $this->get($id);
                $registro->fill($request->all())->save();
                return $registro;
            }

            public function getAllFriends()
            {
                // $users = User::where('id', '!=', auth()->user()->id)->get();
                $bp = User::where('friends')->get();
                $users = friends::all();
                return response()->json([
                    "success"=>true,
                    "message"=>"Registro con éxito",
                    "user"=>$bp,
                        
                ],200);
            }
            // public function getAll()
            // {
            // $registro = registro::all();
            //     return $registro;
            // }
            // public function add(Request $request)
            // {
            // 	$registro = registro::create($request->all());
            // 	return $registro;
            // }
            // public function get($id){
            //     $registro = registro::find($id);
            //     return $registro;
            // }
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
