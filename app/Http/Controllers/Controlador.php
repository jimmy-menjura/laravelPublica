<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\friends;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\File;

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
        try{
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
            $user = User::create([
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                'nickName' => $request->get('nickName'),
                'fullName' => $request->get('fullName'),
                'birthdate' => preg_replace("/T.*/","", $request->get('birthdate'))
                ]);
            if($request->hasFile('image')){
                $probar = $request->file('image')->getClientOriginalName();
                $imagenes = $request->file('image')->storeAs("public/archivo_imagenes/".  $user->id , $probar);
                $image = Storage::url($imagenes);
                $user->update(['image'=> $image]);
            }
            // $user = registro::create($request->all());

                $token = JWTAuth::fromUser($user);

                return response()->json(compact('user','token'),201);
            }catch(Exception $e){
                return response()->json([
                    "resp" => true,
                    "Mensaje" => 'Falló al insertar',
                    "error " => $e
                ],304);
            }
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
                
            public function editarPerfil(Request $request,$id){
                    $user = User::find($request->id);
                    $user->email = $request->email;
                    $user->nickname = $request->nickname;
                    $user->fullname = $request->fullname;
                    $user->birthdate = $request->birthdate;
                    $actualizado = $user->save();
                
                
                // $registro = $this->get($id);
                //     $registro->fill($request->all())->save();
                //     return $registro;
                // dd($id);
                //     if($request->hasFile('image')){
                //         print("guardar imagen2");
                //         $request->file('image')->getClientOriginalName();
                //         $imagenes = $request->file('image')->storeAs("public/archivo_imagenes");
                //         if($id->image != ''){
                //             print("guardar imagen3");
                //             unlink(storage_path("public/archivo_imagenes"));
                //         }
                //         $image = Storage::url($imagenes);
                //         $id->update(['image'=> $image]);
                //     }
                //     $id->update($request->only(["email","nickname","fullname","birthdate"]));
                //     print("guardar imagen1");
                //     print("guardar imagen4");
                    // dd($request->only(["email","nickname","fullname","birthdate",parse_str($image)]));
                    // $user = new User();
                    // $user->email = $request->email;
                    // $user->nickname = $request->nickname;
                    // $user->fullname = $request->fullname;
                    // $user->birthdate = $request->birthdate;
                    // $user->image = $image;
                    // $user->save();
                    // $registro = $this->get($id);
                    // $registro->fill($request->all())->save();
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
        public function guardarImagenPerfil(Request $request,User $id){
            
                    if($request->hasFile('image')){
                        $probar = $request->file('image')->getClientOriginalName();
                        $imagenes = $request->file('image')->store("public/archivo_imagenes/". $id->id);
                        if($id->image != ''){
                            unlink(public_path($id->image));
                        }
                        $image = Storage::url($imagenes);
                        $actualizado = $id->update(['image'=> $image]);
                        return response()->json([
                        "resp" => true,
                        "idActualizado" => $id->id,
                        "imagenActualizado" => $image,
                        "Mensaje" => 'Actualizado exitosamente'
                    ],200);
                    }
        }
        public function verificatedPassword(Request $request){
            $users =User::find(auth()->user()->id)->password;
            $passwordReq = $request->get('password');
            if (Hash::check($passwordReq, $users)) {
                return response()->json([
                    "success"=>true
                ],200);
            }else{
                return response()->json([
                    "success"=>false
                ],200);
            }
        }
        public function updatePass(Request $request){
            User::where('id', auth()->user()->id)->update(['password' => Hash::make($request->get('newPassword'))]);
            return response()->json([
                "resp" => true,
                "Mensaje" => 'Actualizado exitosamente'
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
