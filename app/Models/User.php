<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\publicaciones;
use App\Models\friends;
use App\Models\Likes;
use App\Models\comments;
use App\Models\Notificaciones;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $fillable = array('email', 'password', 'nickName', 'fullName', 'birthdate', 'image','watchpublications');
    
    public function publicaciones(){
        return $this->hasMany(publicaciones::class,'users_id');
    }
    public function friends(){
        return $this->hasMany(friends::class);
    }
    public function Likes(){
        return $this->hasMany(Likes::class);
    }
    public function Comments(){
        return $this->hasMany(comments::class);
    }
    public function chats(){
        return $this->belongsToMany('App\Models\chat');
    }
    public function mensajes()
    {
        return $this->hasMany('App\Models\Mensaje');
    }
    public function Notificaciones(){
        return $this->hasMany(Notificacion::class);
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'remember_token',
    ];
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
