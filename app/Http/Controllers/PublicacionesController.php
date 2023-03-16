<?php

namespace App\Http\Controllers;

use App\Models\publicaciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'users_id'=>$this->publicaciones->user_id
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

        $userRecep = User::with([
            'publicaciones' => function($q) {
            $q->orderBy('publicaciones.created_at', 'desc');
                $q->whereHas('users.friends',function($o) {
                    $o->where('user_friend','=',auth()->user()->id)
                    ->where('status','=',2); 
            });
        }])->get();

        // $userRecep = User::with([
        //     'publicaciones' => function(MorphTo $morphTo){
        //         $morphTo->constrain([
        //             Friends::class => function (Builder $query) {
        //                 $query->where('friends.user_friend', '=', auth()->user()->id)
        //                 ->where('friends.status','=',2);
        //             }
        //         ]);
        //     }
        //     ])
            //   ->join('friends','users.id', '=' ,'friends.user_id')
            //   ->where('friends.user_friend', '=', auth()->user()->id)
            //   ->where('friends.status','=',2) 
            //   ->get();
            // print_r($userRecep);
            // return;
            
            // $UserSend =   User::with(['publicaciones' => function (MorphTo $morphTo) {
            //     $morphTo->constrain([
            //         friends::class => function (Builder $query) {
            //             // $query->join('publicaciones','friend.user_friend','=','publicaciones.users_id')
            //             $query->where('friends.user_id','=', auth()->user()->id)
            //             ->where('friends.status','=',2);
            //         }
            //     ]);
            // }])->get();

            $UserSend = User::withWhereHas('publicaciones', function ($query) {
                $query->join('friends','friends.user_friend','=','publicaciones.users_id')
                ->where('friends.user_id','=', auth()->user()->id)
                ->where('friends.status','=',2);
            })->get();
        // $UserSend = User::withWhereHas('publicaciones', function(Builder $q) {
        //     $q->orderBy('publicaciones.created_at', 'desc')
        //     ->join('friends','friend.user_friend','=','publicaciones.users_id')
        //     ->where('friends.user_id','=', auth()->user()->id)
        //     ->where('friends.status','=',2);
        // }
        // )
        // ->join('friends','friends.user_friend','=','users.id')
        // ->join('publicaciones','friend.user_friend','=','publicaciones.users_id')
        // ->where('friends.user_id','=', auth()->user()->id)
        // ->where('friends.status','=',2)
        // ->get();
            // print_r($UserSend);
            // return;
        // $myPublic = User::with('publicaciones') 
        //     ->join('publicaciones','users.id','=','publicaciones.user_id')
        //     ->where('users.id','=',auth()->user()->id);
        $myPublic = User::with([
            'publicaciones' => function($q){
                $q->where('users_id','=',auth()->user()->id)
                ->orderBy('created_at', 'desc');
            }
            ])->where('users.id','=',auth()->user()->id)
            ->get();

        if(count($userRecep) > 0 || count($UserSend) > 0  || count($myPublic) > 0 ){
            $object1 = (object)array();
            $object2 = (object)array();
            $object3 = (object)array();
            $newObject = json_encode($myPublic,TRUE);
            $newArr = [];
            $createArrUpdate =  array();
            for ($i=0; $i < count($userRecep); $i++) { 
                $object1 = (object) $userRecep[$i];
            }
            for ($i=0; $i < count($UserSend); $i++) { 
                $object2 = (object) $UserSend[$i];
            } 
            for ($i=0; $i < count($myPublic); $i++) { 
                $object3 = (object) $myPublic[$i];
            }
            // $jsonDecode1 = json_uncode($object1,true);
            // $jsonDecode2 = json_uncode($object2,true);
            // $jsonDecode3 = json_uncode($object3,true);
            if(empty((array)$object2) && empty((array)$object1) && empty((array)$object3)){
                $arrPublications = array();
            }
            else if(empty((array)$object1) && empty((array)$object2)){
                $arrPublications =  array($object3);
            }
            else if(empty((array)$object2) && empty((array)$object3)){
                $arrPublications = array($object1);
            }
            else if(empty((array)$object1) && empty((array)$object3)){
            $arrPublications = array($object2);
            }
            else if(empty((array)$object1)){
                $arrPublications = array($object2,$object3);
            }
            else if(empty((array)$object3)){
                $arrPublications = array($object1,$object2);
            }
            else if(empty((array)$object2)){
                $arrPublications = array($object1,$object3);
            }

            else{
                $arrPublications =  array($object1,$object2,$object3);
               
            }
            /**
             * Desde acá podemos ordernar las publicaciones por fecha de creación.
             */
            // if (!empty($arrPublications)){
            // for ($i=0; $i <= count($arrPublications) ; $i++) { 
                // $publicationsOrders = usort($publications, function($a, $b) {
                    //         return strtotime($a['created_at']) - strtotime($b['created_at']);
                    //     });
        //     }
        // }
        }
        // $user = View::make('view_name')->with('baseProduct', $bp);
        //  $user = User::find($this->publicaciones->user_id);
    	$publicacio = publicaciones::all();
        return response()->json([
            "success"=>true,
            "message"=>"arrPublications con éxito",
            "userPublic"=>$arrPublications,
                
        ],200);
    }

    public function get($id){
        $publicacion = User::with('publicaciones')->find($id);
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
