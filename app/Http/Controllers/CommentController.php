<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\comments;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function getCommentByUserAndPublication($id){
        $publicacion =  DB::table('comments')
            ->where('comments.user_id','=',auth()->user()->id)
            ->where('comments.publicacion_id','=',$id)
            ->get('comments.id');
        return $publicacion;
    }
    public function get($id){
        $publicacion =  DB::table('comments')
            ->join('users','users.id','=','comments.user_id')
            ->where('comments.publicacion_id','=',$id)
            ->get();
        return $publicacion;
    }

    public function deleteComment($id){ 
        $like = comments::find($id);
        $like->delete();
        return $like;
    }

    public function saveComment(Request $comment){ 
        $registro = comments::create($comment->all());
        return response()->json([
            "success"=>true,
            "message"=>"like guardado con éxito",
            "registrado" => $registro
        ],200);
    }
}
