<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments extends Model
{
    protected $fillable = array('comments','publicacion_id','user_id');

    
    public function publicaciones(){
        return $this->hasMany(publicaciones::class);
    }
    public function Users()
    {
        return $this->belongsTo(User::class);
    }
}
