<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mensajes extends Model
{
    use HasFactory;

    protected $fillable = array('user_auth','message','to');

    public function users(){
        return $this->belongsToMany('App\Models\User');
    }
    public function mensaje(){
        return $this->hasMany('App\Models\Mensaje');
    }
}
