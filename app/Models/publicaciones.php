<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Friends;
use App\Models\Likes;
use App\Models\Comments;
use Illuminate\Foundation\Auth\User as Authenticatable;

class publicaciones extends Model
{
    protected $table = 'publicaciones';
    protected $fillable = array('description','image','users_id');
    
    public function Users()
    {
        return $this->belongsTo(User::class,'users_id');
    }
    public function Friends()
    {
        return $this->belongsTo(Friends::class,'users_id','user_id');
    }
    public function Likes()
    {
        return $this->belongsTo(Likes::class);
    }
    public function Comments()
    {
        return $this->belongsTo(Comments::class);
    }
    
}
