<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\publicaciones;
use App\Models\friends;
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $fillable = array('email', 'password', 'nickName', 'fullName', 'birthdate', 'image');
    
    public function publicaciones(){
        return $this->hasMany(publicaciones::class);
    }
    public function friends(){
        return $this->hasMany(friends::class);
    }
    public function chats(){
        return $this->belongsToMany('App\Models\chat');
    }
    public function mensajes()
    {
        return $this->hasMany('App\Models\Mensaje');
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
