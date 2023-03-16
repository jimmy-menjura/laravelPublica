<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class friends extends Model
{
    protected $fillable = array('user_friend','status','user_id');
    

    public function publicaciones(){
        return $this->hasMany(publicaciones::class,'users_id','user_id');
    }
    public function Users()
    {
        return $this->belongsTo(User::class);
    }
}
