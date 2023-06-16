<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\publicaciones;

class Likes extends Model
{
    protected $fillable = array('like','publicacion_id','user_id');

    
    public function publicaciones(){
        return $this->hasMany(publicaciones::class);
    }
    public function Users()
    {
        return $this->belongsTo(User::class);
    }
    
}
