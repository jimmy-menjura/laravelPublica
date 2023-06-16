<?php

namespace App\Http\Controllers;

use App\Models\publicaciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use \stdClass;

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
        /**
         * ESTE QUERY SIRVE SIEMPRE Y CUANDO SOLO SE DEBE ORDENAR POR PUBLICACIONES QUE REALIZÓ ESTÁ PERSONA.
         */
        // $userRecep = User::with([
        //     'publicaciones' => function($q) {
        //     $q->orderBy('publicaciones.created_at', 'desc');
        //         $q->whereHas('users.friends',function($o) {
        //             $o->where('user_friend','=',auth()->user()->id)
        //             ->where('status','=',2); 
        //     });
        // }])->get();

        $userRecep = DB::table('users')
            ->join('friends','users.id','=','friends.user_id')
            ->join('publicaciones','users.id','=','publicaciones.users_id')
            ->leftJoin('likes',function ($query){
                $query->on('likes.publicacion_id','=','publicaciones.id');
                $query->on('likes.user_id','=',DB::raw(auth()->user()->id));
            })
            ->where('friends.user_friend','=',auth()->user()->id)
            ->where('status','=',2)
            ->orderBy('publicaciones.created_at', 'desc')
            ->get(['publicaciones.id',DB::raw('(select count(likes.like) from likes 
            where likes.publicacion_id = publicaciones.id ) as contadorLikes'),'likes.like','likes.id as likeId','publicaciones.users_id as user_id', DB::raw("false as myPublic"), 'users.nickname','users.fullname',
            'users.image as ImagenUser','publicaciones.image as imagenPublica',
            'publicaciones.created_at','publicaciones.updated_at','publicaciones.description']);

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

        /**
         * ESTE QUERY SIRVE SIEMPRE Y CUANDO SOLO SE DEBE ORDENAR POR PUBLICACIONES QUE REALIZÓ ESTÁ PERSONA.
         */
            // $UserSend = User::withWhereHas('publicaciones', function ($query) {
            //     $query->join('friends','friends.user_friend','=','publicaciones.users_id')
            //     ->where('friends.user_id','=', auth()->user()->id)
            //     ->where('friends.status','=',2);
            // })->get();


            $UserSend = DB::table('users')
            ->join('friends','users.id','=','friends.user_friend')
            ->join('publicaciones','users.id','=','publicaciones.users_id')
            ->leftJoin('likes',function ($query){
                $query->on('likes.publicacion_id','=','publicaciones.id');
                $query->on('likes.user_id','=',DB::raw(auth()->user()->id));
            })
            ->where('friends.user_id','=',auth()->user()->id)
            ->where('status','=',2)
            ->orderBy('publicaciones.created_at', 'desc')
            // ->get();
            ->get(['publicaciones.id',DB::raw('(select count(likes.like) from likes 
            where likes.publicacion_id = publicaciones.id ) as contadorLikes'),'likes.like','likes.id as likeId','publicaciones.users_id as user_id', DB::raw("false as myPublic"),'users.nickname','users.fullname'
            ,'users.image as ImagenUser','publicaciones.image as imagenPublica',
            'publicaciones.created_at','publicaciones.updated_at','publicaciones.description']);

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

        /**
         * ESTE QUERY SIRVE SIEMPRE Y CUANDO SOLO SE DEBE ORDENAR POR PUBLICACIONES QUE REALIZÓ ESTÁ PERSONA.
         */
        // $myPublic = User::with([
        //     'publicaciones' => function($q){
        //         $q->where('users_id','=',auth()->user()->id)
        //         ->orderBy('created_at', 'desc');
        //     }
        //     ])->where('users.id','=',auth()->user()->id)
        //     ->get();
            
            $myPublic = DB::table('users')
            ->join('publicaciones','publicaciones.users_id','=','users.id')
            ->leftJoin('likes',function ($query){
            $query->on('likes.publicacion_id','=','publicaciones.id');
            $query->on('likes.user_id','=',DB::raw(auth()->user()->id));
            })
            ->where('publicaciones.users_id','=',auth()->user()->id)
            ->orderBy('publicaciones.created_at', 'desc')
            // ->get();
            ->get(['publicaciones.id','likes.like',DB::raw('(select count(likes.like) from likes 
            where likes.publicacion_id = publicaciones.id ) as contadorLikes'),'likes.id as likeId', 'publicaciones.users_id as user_id', DB::raw("true as myPublic"),'users.nickname','users.fullname',
            'users.image as ImagenUser','publicaciones.image as imagenPublica',
            'publicaciones.created_at','publicaciones.updated_at','publicaciones.description']);


        // if(count($userRecep) > 0 || count($UserSend) > 0  || count($myPublic) > 0 ){
            $arrPublications = array();
                if(count($userRecep) > 0){
                for ($i=0; $i < count($userRecep); $i++) { 
                // $object1 = $userRecep[$i];
                array_push($arrPublications, $userRecep[$i]);
                }
                }else
                $object1 = (object)array();    
                if(count($UserSend) > 0){
                for ($i=0; $i < count($UserSend); $i++) { 
                    // $object2 = $UserSend[$i];
                    array_push($arrPublications, $UserSend[$i]);
                }
                }else
                $object2 = (object)array();
                if(count($myPublic) > 0){
                    // echo count($myPublic);
                for ($i=0; $i < count($myPublic); $i++) { 
                array_push($arrPublications, $myPublic[$i]);
                // $object3 = $myPublic[$i];
                // return $object3;    
                }
                // print_r($this->myPublications($myPublic));
                }else 
                $object3 = (object)array();
        // }
            //  print_r($stack);
            
            // $jsonDecode1 = json_uncode($object1,true);
            // $jsonDecode2 = json_uncode($object2,true);
            // $jsonDecode3 = json_uncode($object3,true);
            // if(empty((array)$object2) && empty((array)$object1) && empty((array)$object3)){
            //     $arrPublications = array();
            // }
            // else if(empty((array)$object1) && empty((array)$object2)){
            //     $arrPublications =  array($object3);
            // }
            // else if(empty((array)$object2) && empty((array)$object3)){
            //     $arrPublications = array($object1);
            // }
            // else if(empty((array)$object1) && empty((array)$object3)){
            // $arrPublications = array($object2);
            // }
            // else if(empty((array)$object1)){
            //     $arrPublications = array($object2,$object3);
            // }
            // else if(empty((array)$object3)){
            //     $arrPublications = array($object1,$object2);
            // }
            // else if(empty((array)$object2)){
            //     $arrPublications = array($object1,$object3);
            // }
            // else{
            //     $arrPublications =  array($object1,$object2,$object3);
               
            // }
            /**
             * Desde acá podemos ordernar las publicaciones por fecha de creación.
             */
            if (!empty($arrPublications)){
                // $result = json_decode($arrPublications, true);
            // for ($i=0; $i <= count($arrPublications) ; $i++) { 
                 usort($arrPublications, function($a, $b) {
                            return strtotime($b->created_at) - strtotime($a->created_at);
                        });
            // }
        }
        
        // $user = View::make('view_name')->with('baseProduct', $bp);
        //  $user = User::find($this->publicaciones->user_id);
    	// $publicacio = publicaciones::all();
        return response()->json([
            "success"=>true,
            "message"=>"arrPublications con éxito",
            "userPublic"=>$arrPublications,
                
        ],200);
    }
    public function get($id){
        $publicacion = User::with(['publicaciones' => function($q) {
                $q->leftJoin('likes',function ($query){
                $query->on('likes.publicacion_id','=','publicaciones.id');
                $query->on('likes.user_id','=',DB::raw(auth()->user()->id));
            });
            $q->orderBy('publicaciones.created_at', 'desc')
            ->select('publicaciones.users_id','publicaciones.id',
            DB::raw('(select count(likes.like) from likes 
            where likes.publicacion_id = publicaciones.id ) as contadorLikes'),'likes.like',
            'likes.id as likeId',
            DB::raw("false as myPublic"),
            'publicaciones.image as imagenPublica',
            'publicaciones.created_at','publicaciones.updated_at','publicaciones.description'
            );
            }])
            ->where('users.id','=',$id)
            ->get();
        return $publicacion;
    }
    public function getPublicationByUser($id){
        $publicacion = publicaciones::where('users_id',auth()->user()->id)
        ->where('id','=',$id)->get();
        return $publicacion;
    }
    public function editar($id, Request $request){
        $publicacion = publicaciones::find($request->id);
        $publicacion->description = $request->description;
        $actualizado = $publicacion->save();

        // $publicacion = $this->get($id);
        // $publicacion->fill($request->all())->save();
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
    public function eliminar($id){ 
        // $url = str_replace('storage','public',) 
        // $delete = publicaciones::where('users_id',auth()->user()->id)
        // ->where('id','=',$id)->get();
        $delete = $this->getPublicationByUser($id);
        // $delete['image']);   
        $url = str_replace('storage','public',$delete[0]['image']) ;
        publicaciones::where('users_id',auth()->user()->id)
        ->where('id','=',$id)->delete();
        Storage::delete($url);
        
        return response()->json([
            "success"=>true,
            "message"=>"Eliminado exitosamente"
        ],200);
    }
    
}
