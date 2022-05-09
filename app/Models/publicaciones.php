<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;

class publicaciones extends Model
{
    protected $table = 'publicaciones';
    protected $fillable = array('description','image','user_id');
    
    public function Users()
    {
        return $this->belongsTo(User::class);
    }
}
