<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Friends;
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
}
