<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Likes;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{

    public function getLikeByUserAndPublication($id){
        $publicacion =  DB::table('likes')
            ->where('likes.user_id','=',auth()->user()->id)
            ->where('likes.publicacion_id','=',$id)
            ->get('likes.id');
        return $publicacion;
    }
    public function get($id){
        $publicacion =  DB::table('likes')
            ->join('users','users.id','=','likes.user_id')
            ->where('likes.publicacion_id','=',$id)
            ->get();
        return $publicacion;
    }

    public function deleteLike($id){ 
        $like = Likes::find($id);
        $like->delete();
        return $like;
    }

    public function saveLike(Request $like){ 
        $registro = Likes::create($like->all());
        return response()->json([
            "success"=>true,
            "message"=>"like guardado con éxito",
            "registrado" => $registro
        ],200);
    }
}
